<?php

namespace App\Http\Controllers;

use App\User;
use App\Reply;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ThreadController extends Controller
{
    public function createThread(Request $request){
        $saved = Thread::store($request);
        Log::debug('printing value of $saved...'.$saved);
        if($saved[0]->id){
            return redirect()->action('ThreadController@showThread', ['threadId' => $saved[0]->id]);
        }
        else{
            return view('home',['saved'=> false,'sections'=> Thread::getSections()]);
        }
    }

    public static function showThread($threadId){
        Log::debug('showthread called savedreply as '.session('savedReply').'and threadid as '.$threadId);
        $data = Thread::select('*')->where('id','=', $threadId)->get();
        Session::put('currThreadId' , ($data[0]->id ?? NULL));
        $userName = User::select('name')->where('id','=',$data[0]->user_id)->get()[0]->name;        
        
        $topComments = Reply::join('users','replies.user_id', '=', 'users.id')->select('users.name','replies.reply','replies.id','replies.has_child','replies.parent')->where([['replies.thread_id',
         '=',$data[0]->id],['replies.parent',0]])->orderby('replies.updated_at','desc')->limit(8)->get();
        $x = session('savedReply') ?? NULL;
        session()->forget('savedReply');
        // $topComments = DB::select(DB::raw("WITH RECURSIVE childReplies AS( SELECT replies.id, reply, parent, name FROM replies INNER JOIN users ON 
        // replies.user_id = users.id WHERE thread_id = :threadID
        // UNION
        // SELECT e.id, e.reply, e.parent, users.name FROM replies e INNER JOIN childReplies s ON s.id = e.parent INNER JOIN users ON 
        // e.user_id = users.id) SELECT * FROM childReplies"),['threadID' => $data[0]->id]);
        Log::debug('showthread() data and username and topcomments',['data'=> $data, 'usr'=>$userName, 'topc'=> $topComments]); 
        return view('showThread',['data' => $data, 'userName'=> $userName, 'savedReply' =>$x, 'topComments' => $topComments]);
    }
    
}