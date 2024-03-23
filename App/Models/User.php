<?php

namespace App\Models;

use App\Libs\Auth\Auth;

class User extends Auth
{
    protected $fillable = ['fullname', 'username', 'password', 'age', 'email'];
    protected $guarded = ['token'];
    protected $hidden = ['password', 'token'];
    protected $unique = ['username', 'email'];
}
