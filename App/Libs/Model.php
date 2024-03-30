<?php

namespace App\Libs;

use App\Libs\Database as DB;
use App\Libs\Response;
use PDO;

/**
 * 
 * @method static \App\Libs\Model select(array $columns)
 * @method static \App\Libs\Model where(string $column, string $value, string $operator = "=")
 * @method static \App\Libs\Model update(array $data, bool $fillable = true)
 * @method static \App\Libs\Model find(int $id)
 * @method static \App\Libs\Model first()
 * @method static \App\Libs\Model orderBy(string $column, string $order = "ASC")
 * @method static \App\Libs\Model limit(int $limit)
 * @method static \App\Libs\Model offset(int $offset)
 * 
 */

class Model extends DB
{
    protected static $model;

    protected $db;
    protected $table;
    protected $primaryKey;
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [];
    protected $guarded = ['*'];
    protected $hidden = [];
    protected $unique = [];

    protected $attributes;

    public function __construct(array|\stdClass $attributes = null)
    {
        $this->db();
        $this->table = $this->table ?? getClassName($this);
        $this->primaryKey = $this->primaryKey ?? $this->table . '_id';
        $this->buildAttributes();
        $this->fill((array) $attributes ?? (array) $this->attributes);
    }

    private function db()
    {
        $this->db = DB::getPdo();
    }

    private function buildAttributes()
    {
        $attributes = new \stdClass();
        $attributes->{$this->primaryKey} = null;
        foreach ($this->fillable as $value) {
            $attributes->$value = null;
        }
        $this->attributes = $attributes;
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->__set($key, $value);
        }
        return $this;
    }

    public function __set($key, $value)
    {
        $this->attributes->$key = $value ?? null;
    }

    public function __get($key)
    {
        return $this->attributes->$key ?? null;
    }

    public function get()
    {
        if ($this->hasData()) {
            return $this->attributes;
        }

        $select = implode(", ", $this->select);

        $sql = "SELECT " . $select . " FROM " . $this->table;

        if (count($this->where) > 0) {
            $where = [];
            foreach ($this->where as $key => $value) {
                $where[] = $key . " " . $value["operator"] . " :" . $key;
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        if (count($this->orderBy) > 0) {
            $order = [];
            foreach ($this->orderBy as $value) {
                $order[] = $value[0] . " " . $value[1];
            }
            $sql .= " ORDER BY " . implode(", ", $order);
        }

        if ($this->limit) {
            $sql .= " LIMIT " . $this->limit;
        }

        if ($this->offset) {
            $sql .= " OFFSET " . $this->offset;
        }

        $stmt = $this->db->prepare($sql);

        $param = [];
        foreach ($this->where as $key => $value) {
            $param[$key] = $value["value"];
        }

        try {
            $stmt->execute($param);
            if ($this->limit == 1) {
                $data = $stmt->fetch(PDO::FETCH_OBJ);
                $this->{$this->primaryKey} = $data->{$this->primaryKey};
                foreach ($data as $key => $value) {
                    if (in_array($key, $this->hidden)) {
                        $this->$key = "******";
                        continue;
                    }
                    $this->$key = $data->{$key};
                }
                return $this;
            }
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);
            $clean = new \stdClass();
            foreach ($data as $key => $value) {
                $clean->{$key} = $value;
                foreach ($value as $k => $v) {
                    if (in_array($k, $this->hidden)) {
                        $clean->{$key}->{$k} = "******";
                    }
                }
            }
            $this->attributes = $clean;
            return $this;
        } catch (\Exception $e) {
            http_response_code(500);
            debug($e->getMessage());
            return (new Response)->withMessage('Server Error.')->withStatus(false)->withHTTPCode(500);
        }
    }

    public function hasData()
    {
        if (is_object($this->attributes) && isset(((array)$this->attributes)[0]) && count((array)$this->attributes) > 1) {
            return true;
        } else if (is_object($this->attributes) && !empty($this->attributes->{$this->primaryKey})) {
            return true;
        }
        return false;
    }

    public function save($obj = false)
    {
        if ($this->hasID()) {
            $sql = "UPDATE " . $this->table . " SET ";
            $columns = [];
            foreach ($this->fillable as $value) {
                if ($value != $this->primaryKey && $this->$value != null) {
                    $columns[] = $value . " = :" . $value;
                }
            }
            $sql .= implode(", ", $columns);
            $sql .= " WHERE " . $this->primaryKey . " = :" . $this->primaryKey;

            $data[$this->primaryKey] = $this->getPrimaryKey();
            foreach ($this->fillable as $value) {
                if ($value != $this->primaryKey && $this->$value != null) {
                    $data[$value] = $this->$value;
                }
            }

            $db = $this->db;
            $stmt = $db->prepare($sql);

            try {
                $db->beginTransaction();

                $stmt->execute($data);
                $db->commit();

                if ($stmt->errorCode() !== '00000') {
                    debug($stmt->errorInfo());
                    if ($obj) {
                        return null;
                    }
                    return (new Response)->withMessage('Server Error.')->withStatus(false)->withHTTPCode(500);
                } else {
                    if ($stmt->rowCount() > 0) {
                        $data = $this->find($data[$this->primaryKey])->attributes;
                        if ($obj) {
                            return $this;
                        }
                        return (new Response)->withMessage('Data updated successfully')->withData($data)->withStatus(true)->withHTTPCode(200);
                    } else {
                        if ($obj) {
                            return null;
                        }
                        return (new Response)->withMessage('No data updated')->withStatus(false)->withHTTPCode(400);
                    }
                }
            } catch (\Exception $e) {
                $db->rollBack();
                if ($obj) {
                    return null;
                }
                http_response_code(500);
                debug($e->getMessage());
                return (new Response)->withMessage('Server Error.')->withStatus(false)->withHTTPCode(500);
            }
        }

        $class = new static;
        $sql = "INSERT INTO " . $class->table . " (";
        $sql .= implode(", ", $this->fillable);
        $sql .= ") VALUES (";
        $sql .= ":" . implode(", :", $this->fillable);
        $sql .= ")";

        $data = [];
        foreach ($this->fillable as $value) {
            $data[$value] = $this->$value;
        }

        $db = $this->db;
        $stmt = $db->prepare($sql);

        try {
            $db->beginTransaction();
            $stmt->execute($data);
            $id = $db->lastInsertId();
            $db->commit();
            if ($stmt->rowCount() > 0) {
                $this->{$this->primaryKey} = intval($id);
                if ($obj) {
                    return $this;
                }
                return (new Response)->withMessage('Data created successfully')->withData($this->find($id)->attributes)->withStatus(true)->withHTTPCode(201);
            } else {
                if ($obj) {
                    return null;
                }
                return (new Response)->withMessage('Failed to create data')->withStatus(false)->withHTTPCode(400);
            }
        } catch (\Exception $e) {
            $db->rollBack();
            if ($obj) {
                return null;
            }
            debug($e->getMessage());
            return (new Response)->withMessage('Server Error.')->withStatus(false)->withHTTPCode(500);
        }
    }

    public static function create($data = [])
    {
        $class = new static;
        $class->fill($data);
        return $class->save();
    }

    public function delete()
    {
        if ($this->hasID()) {
            return $this->destroy($this->getPrimaryKey());
        } else {
            return (new Response)->withMessage('No data deleted')->withStatus(false)->withHTTPCode(400);
        }
    }

    public static function destroy($id)
    {
        $class = new static;
        $sql = "DELETE FROM " . $class->table . " WHERE " . $class->primaryKey . " = :id";
        $stmt = $class->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        if ($stmt->errorCode() !== '00000') {
            debug($stmt->errorInfo());
            return (new Response)->withMessage('Server Error.')->withStatus(false)->withHTTPCode(500);
        } else {
            if ($stmt->rowCount() > 0) {
                return (new Response)->withMessage('Data deleted successfully')->withData([$class->primaryKey => $id])->withStatus(true)->withHTTPCode(200);
            } else {
                return (new Response)->withMessage('No data deleted')->withStatus(false)->withHTTPCode(400);
            }
        }
    }

    public function newUpdate($data = [], $fillable = true)
    {
        $sql = "UPDATE " . $this->table . " SET ";
        $columns = [];
        $newData = [];

        if ($fillable) {
            foreach ($this->fillable as $value) {
                if ($value != $this->primaryKey && $data[$value] != null) {
                    $columns[] = $value . " = :" . $value;
                    $newData[$value] = $data[$value];
                }
            }
        } else {
            foreach ($data as $key => $value) {
                if ($key != $this->primaryKey) {
                    $columns[] = $key . " = :" . $key;
                    $newData[$key] = $value;
                }
            }
        }

        if (count($columns) == 0) {
            return false;
        }

        $sql .= implode(", ", $columns);
        $sql .= " WHERE " . $this->primaryKey . " = " . $this->getId()
            . " LIMIT 1;";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($newData);
            return $this->find($this->getId());
        } catch (\Exception $e) {
            debug($e->getMessage());
            return (new Response)->withMessage('Server Error.')->withStatus(false)->withHTTPCode(500);
        }
    }

    public function hasField($field)
    {
        return in_array($field, $this->fillable);
    }

    public function hasId()
    {
        return isset(((array) $this->attributes)[$this->primaryKey]);
    }

    public function getId()
    {
        return ((array) $this->attributes)[$this->primaryKey];
    }

    public function getPrimaryKey()
    {
        return ((array) $this->attributes)[$this->primaryKey];
    }

    public function getAttributes($array = false): array|\stdClass
    {
        if ($array) {
            return (array) $this->attributes;
        }
        return $this->attributes;
    }

    public function __destruct()
    {
        $this->db = null;
    }

    public function __call($method, $args)
    {
        switch ($method) {
            case "find":
                return $this->where($this->primaryKey, $args[0] ?? $args['id']);
                break;
            case "update":
                return $this->newUpdate($args[0] ?? $args['data'], $args[1] ?? $args['fillable'] ?? true);
                break;
            case "first":
                $this->limit = 1;
                return $this->get();
                break;
            case "select":
                $select = $args[0] ?? $args['columns'];
                foreach ($select as $value) {
                    if (!$this->hasField($value)) {
                        Response::make(message: 'Column ' . $value . ' not found.', status: false, httpCode: 400);
                        exit;
                    }
                }
                $this->select = $select;
                return $this;
                break;
            case "where":
                try {
                    if (is_array($args[0] ?? isset($args['columns']))) {
                        foreach ($args[0] ?? $args['columns'] as $key => $column) {
                            if (!$this->hasField($key)) {
                                Response::make(message: 'Column ' . $key . ' not found.', status: false, httpCode: 400);
                                exit;
                            }
                            if (!isset($column['value'])) {
                                Response::make(message: 'Invalid where clause.', status: false, httpCode: 400);
                                exit;
                            }
                            $this->where[$key] = ["value" => $column[0] ?? $column['value'], "operator" => $column[1] ?? $column['operator'] ?? "="];
                        }
                        return $this;
                    }
                    $this->where[$args[0] ?? $args['column']]
                        = ["value" => $args[1] ?? $args['value'] ?? null, "operator" => $args[2] ?? $args['operator'] ?? "="];
                    return $this;
                } catch (\Exception $e) {
                    Response::make(message: 'Invalid where clause.', status: false, httpCode: 400);
                    exit;
                }
                break;
            case "orderBy":
                $this->orderBy[] = [$args[0] ?? $args['column'], $args[1] ?? $args['order'] ?? "ASC"];
                return $this;
                break;
            case "limit":
                $this->limit = $args[0] ?? $args['limit'];
                return $this;
                break;
            case "offset":
                $this->offset = $args[0] ?? $args['offset'];
                return $this;
                break;
        }
    }

    public static function __callStatic($method, $args)
    {
        static::$model = new static;
        return static::$model->$method(...$args);
    }
}
