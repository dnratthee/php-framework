<?php

namespace App\Libs\Auth;

use App\Libs\Model;

// TODO :: Add a method to check if the user is authorized to access a resource
class Auth extends Model
{
    public static function check()
    {
        return isset($_SESSION['user']);
    }

    public static function login($user)
    {
        $user = new static($user);
        $token = md5(uniqid(microtime(), true));
        $_SESSION['user'] = $user->attributes;
        $_SESSION['token'] = $token;
        $user->update([$user->primaryKey => $user->id, 'token' => $token], false);
        $user->token = $token;
        return $user;
    }

    public static function logout()
    {
        unset($_SESSION['user']);
    }

    public static function getToken()
    {
        return $_SESSION['token'];
    }
}
