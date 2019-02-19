<?php

namespace App\Http\Controllers;

use App\User;
use App\Reply;
use App\Thread;
use App\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function renderProfile($userId){
        $totalThreads = Thread::select('*')->where('threads.user_id','=',$userId)->count();
        $totalReplies = Reply::select('*')->where('replies.user_id','=',$userId)->count();
        $email = User::select('email')->where('users.id','=',$userId)->get();
        $name = User::select('name')->where('users.id','=',$userId)->get();
        return view('profile', ['totalThreads' => $totalThreads, 'totalReplies'=>$totalReplies, 'email'=>$email, 'name' => $name]);
    }

    public function postMsg(Request $request){
        Log::info($request->getContent());
        $x = $request->input('from_id');
        $temp = new Message;
        $temp->from_id = $request->input('from_id');
        $temp->to_id = $request->input('to_id');
        $temp->msg = $request->input('msg');
        $temp->created_at = Carbon::now()->toDateTimeString();
        $result = $temp->save();
        return response()->json($result);
    }
}
