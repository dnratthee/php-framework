<?php

namespace App\Libs;

class Response
{
    protected $data = [];
    protected $message = null;
    protected $status = true;
    protected $HTTPCode = 200;

    public function __construct($data = [], $message = null, $status = true, $httpCode = 200)
    {
        $this->data = $data;
        $this->message = $message;
        $this->status = $status;
        $this->HTTPCode = $httpCode;
    }

    public static function make($data = [], $message = null, $status = true, $httpCode = 200)
    {
        Request::isApi() ? static::json($data, $message, $status, $httpCode) : static::html($data, $message, $status, $httpCode);
        return new Response($data, $message, $status, $httpCode);
    }

    public static function html($data = [], $message = null, $status = true, $httpCode = 200)
    {
        if ($data instanceof Response) {
            if (!headers_sent()) {
                header('Content-Type: text/html');
            }
            if (is_string($data)) {
                echo $data;
            } else {
                echo json_encode($data, JSON_PRETTY_PRINT);
            }
            return;
        }
        $response = new Response($data, $message, $status, $httpCode);
        $response->toHTML();
    }

    public static function toHTML($data = [], $message = null, $status = true, $httpCode = 200)
    {
        if (!headers_sent()) {
            header('Content-Type: text/html');
        }
        if (is_string($data)) {
            echo $data;
        } else {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }
    }

    public static function json($data = [], $message = null, $status = true, $httpCode = 200)
    {
        if ($httpCode >= 400) {
            $status = false;
        }
        if ($data instanceof Response) {
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            $data->toJson();
            return;
        } else if (is_object($data) && method_exists($data, 'getAttributes')) {
            $data = (array) $data->getAttributes();
        }
        $response = new Response($data, $message, $status, $httpCode);
        $response->toJson();
    }

    public function toJson()
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
            http_response_code($this->HTTPCode);
        }
        echo json_encode([
            'status' => $this->status,
            'message' => $this->message,
            'data' => (array) $this->data
        ]);
        exit;
    }

    public static function back()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public static function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    public static function render($view, $data = [])
    {
        $view = str_replace('.', '/', $view);
        $view = __DIR__ . '/../Views/' . $view . '.view.php';
        if (file_exists($view)) {
            extract($data);
            include $view;
        } else {
            echo 'View not found';
        }
    }

    public function withMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function withData($data)
    {
        $this->data = (array) $data;
        return $this;
    }

    public function withStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function withHTTPCode($httpCode)
    {
        $this->HTTPCode = $httpCode;
        return $this;
    }
}
