<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Event;
use App\Events\WebsocketDemoEvent;

class MainController extends Controller
{
    function mainPage()
    {
        // Event::dispatch(new WebsocketDemoEvent('data'));
        // broadcast(new WebsocketDemoEvent('data'));
        return view('mainPage');
    }
}
