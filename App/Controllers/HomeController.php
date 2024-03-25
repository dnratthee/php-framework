<?php

namespace App\Controllers;

use App\Libs\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $this->render('index');
    }
}
