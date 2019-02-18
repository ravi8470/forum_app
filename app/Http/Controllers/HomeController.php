<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }
    public function renderHome(){
        Log::debug('home contrllr renderhome()'.dirname(__DIR__));
        $sections = Thread::getSections();
        return view('home', ['sections' => $sections]);
        // dd($sections);
    }



}
