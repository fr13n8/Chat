<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    function mainPage()
    {
        return view('mainPage');
    }
}
