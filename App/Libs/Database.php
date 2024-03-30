<?php

namespace App\Libs;

use PDO;

class Database extends Container
{
    static protected $pdo;
    static private $conf;

    protected $select = ['*'];
    protected $table;
    protected $where = [];
    protected $orderBy = [];
    protected $limit = 20;
    protected $offset;

    static protected function getPdo()
    {
        $db = Config::get('db');

        $default = $db['default'];
        self::$conf = $db['connections'][$default];

        self::$pdo = self::createPdoConnection(
            self::$conf['driver'] . ':host=' . self::$conf['host'] .
                ';port=' . self::$conf['port'] . ';dbname=' . self::$conf['database'] . ';charset=' . self::$conf['charset'],
            self::$conf['username'],
            self::$conf['password'],
            self::$conf['options']
        );

        return self::$pdo;
    }

    static private function createPdoConnection($dsn, $username, $password, $options)
    {
        return new PDO($dsn, $username, $password, $options);
    }
}
