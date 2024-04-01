<?php

namespace App\Libs\Auth;

use App\Libs\Controller as BaseController;
use App\Models\User;
use App\Libs\Request;
use App\Libs\Response;

class Controller extends BaseController
{
    public function auth(Request $request)
    {
        $user = User::where(column: 'username', value: $request->username)
            ->where(column: 'password', value: $request->password)
            ->first();

        if ($user->hasData()) {
            $user = User::login($user);
            Response::json(data: $user, message: 'Login successful');
        } else {
            Response::json(message: 'Invalid username or password', httpCode: 401);
        }
    }

    public function getAll(Request $request)
    {
        Response::json(
            User::all()->get()
        );
    }

    public function getOne(Request $request)
    {
        Response::json(
            User::find($request->id)
                ->get()
        );
    }

    public function store(Request $request)
    {
        $user = User::create($request->all());
        if ($user) {
            Response::json(data: $user, message: 'User created successfully');
        } else {
            Response::json(message: 'Failed to create user', httpCode: 500);
        }
    }

    public function logOut(Request $request)
    {
        if (User::logout()) {
            Response::json(message: 'Logged out successfully');
        } else {
            Response::json(message: 'Failed to logout', httpCode: 500);
        }
    }
}
