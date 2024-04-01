<?php

namespace App\Libs;

use App\Libs\Response;
use App\Libs\Request;

class Controller extends Response
{
    protected function index(Request $request)
    {
        $this->render('index');
    }
}
