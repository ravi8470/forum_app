<?php

namespace App\Http\Controllers;

use App\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ThreadController;

class ReplyController extends Controller
{
    public function postReply(Request $request){
        $savedReply = Reply::storeReply($request);
        Session::put('savedReply', $savedReply);
        return redirect()->action('ThreadController@showThread', ['threadId' => session('currThreadId')]);
        //return ThreadController::showThread(session('currThreadId'), $savedReply);
        
    }
    // public function getChildReplies($parent){
    //     //Log::debug('getChildReplies of ReplyController called wiht parent as : '.$parent);
    //     $childReplies = Reply::join('users','replies.user_id', '=', 'users.id')->select('users.name','replies.reply','replies.id','replies.has_child','replies.parent')->where([['replies.parent',
    //     '=',$parent]])->orderby('replies.updated_at','desc')->get();
    //     //dd($childReplies);

    //     return response()->json($childReplies);
    // }
}
