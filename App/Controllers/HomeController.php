<?php

namespace App\Controllers;

use App\Libs\Controller;
use App\Models\Journal;

class HomeController extends Controller
{
    public function index()
    {
        $this->render('index');
    }
}
