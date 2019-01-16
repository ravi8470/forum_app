<?php

namespace App\Http\Controllers;

use App\User;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadController extends Controller
{
    public function create_thread(Request $request){
        $saved = Thread::store($request);
        return view('home',['saved'=> $saved,'sections'=> Thread::getSections()]);
    }
    
}
