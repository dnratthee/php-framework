<?php

namespace App\Libs\Auth;

use App\Libs\Request;
use App\Libs\Response;
use App\Libs\Routing\Route;

trait Authorizable
{
    public function __construct()
    {
        session_start();
        $this->authorize();
        parent::__construct();
    }

    public function authorize()
    {
        if (!Auth::check()) {
            return Response::json(['message' => 'Unauthorized'], 401);
        }
    }
}
