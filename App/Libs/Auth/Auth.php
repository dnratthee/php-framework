<?php

namespace App\Libs\Auth;

use App\Libs\Model;
use App\Libs\Request;
use App\Models\User;

class Auth extends Model
{
    public static function check()
    {
        if (isset($_SESSION['token'])) {
            $token = $_SESSION['token'];
            $user = new User;
            $user = $user->where('token', $token)->first();
            if ($user) {
                $_SESSION['user'] = $user;
                return true;
            }
        }

        if (Request::header('Authorization')) {
            $token = Request::header('Authorization');
            $token = str_replace('Bearer ', '', $token);
            $user = new User;
            $user = $user->where('token', $token)->first();
            if ($user) {
                $_SESSION['user'] = $user;
                $_SESSION['token'] = $token;
                return true;
            }
        }
        return false;
    }

    public static function login($user)
    {
        session_start();
        $token = md5(uniqid(microtime(), true));
        $_SESSION['user'] = $user->attributes;
        $_SESSION['token'] = $token;
        $user->update([$user->primaryKey => $user->id, 'token' => $token], false);
        $user->token = $token;
        return $user;
    }

    public static function create($data = [])
    {
        $class = new static;
        $class->fill($data);
        $user = $class->save(true);
        return $class->login($user);
    }

    public static function logout()
    {
        session_start();
        $token = null;
        if (isset($_SESSION['token'])) {
            $token = $_SESSION['token'];
        }
        if (Request::header('Authorization') != "" && !isset($token)) {
            $token = Request::header('Authorization');
            $token = str_replace('Bearer ', '', $token);
        }
        if (!$token) {
            return false;
        }
        unset($_SESSION['token']);
        unset($_SESSION['user']);
        $user = new User;
        try {
            $user = $user->where('token', $token)->first();
            if (!$user) {
                return false;
            }
            $user->update([$user->primaryKey => $user->id, 'token' => null], false);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getToken()
    {
        return $_SESSION['token'];
    }
}
