<?php

namespace App\Libs\Helper;

class Env
{
    private $env_path;
    private $tmp_env;

    function __construct()
    {
        $env_path = __DIR__ . "/../../../.env";
        $this->env_path = $env_path;

        $tmp_env = [];

        if (file_exists($this->env_path)) {
            $lines = file($this->env_path);

            foreach ($lines as $line) {
                $line = trim($line);

                $line_is_comment = (substr(trim($line), 0, 1) == '#') ? true : false;
                if ($line_is_comment || empty(trim($line)))
                    continue;

                if ($line != "") {
                    $env_ex = explode("=", $line);
                    $env_name = trim($env_ex[0]);
                    $env_value = isset($env_ex[1]) ? trim($env_ex[1]) : "";

                    $tmp_env[$env_name] = $env_value;
                }
            }
        }

        $this->tmp_env = $tmp_env;
    }

    function get($key, $default = null)
    {
        return $this->tmp_env[$key] ?? $default;
    }
}
