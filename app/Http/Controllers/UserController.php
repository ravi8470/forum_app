<?php

namespace App\Http\Controllers;

use App\User;
use App\Reply;
use App\Thread;
use App\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function renderInbox(){
        $id = Auth::user()->id;
        // $newMsg = Message::join('users','messages.from_id','=','users.id')->select('from_id', 'msg', 'messages.created_at','users.name')->where([['to_id','=',$id],['seen',false]])->orderBy('messages.created_at','desc')->get();
        // dd($newMsg); 
        $allMsg = Message::join('users','messages.from_id','=','users.id')->select('from_id', 'msg', 'messages.created_at','users.name','seen')->where('to_id','=',$id)->orderBy('messages.created_at','desc')->get();
        // dd($allMsg);
        $uniqueSenders = array_unique($allMsg->pluck('from_id','name')->toArray());
        // dd($uniqueSenders);
        $newMsgCountPerUser = array();
        foreach($uniqueSenders as $r => $s){
            $newMsgCountPerUser[$s]= 0;
        }
        // dd($newMsgCountPerUser);
        foreach($uniqueSenders as $z => $x){
            foreach($allMsg as $y){
                if($y->from_id == $x && $y->seen == false){
                    $newMsgCountPerUser[$x]++;
                }
            }
        }
        // dd($p);
        // dd($uniqueSenders);
        // $uniqueMsgCount = array();
        // foreach ($uniqueSenders as $key => $value) {
        //     $uniqueMsgCount[$value] = Message::select('seen')->where([['from_id','=',$value],['seen','=',false],['to_id','=',$id]])->get()->count();
        // }
        // dd($uniqueMsgCount);
        // $newMsgCount = $newMsg->count();
       
        // if($newMsgCount > 0){
            return view('inbox',['uniqueSenders' =>$uniqueSenders, 'newMsgCountPerUser' => $newMsgCountPerUser]);
        // }
        // else{
        //     return view('inbox',['allMsg' => $allMsg, 'uniqueSenders' =>$uniqueSenders]);
        // }
        
    }
    
    public function getConvo($from_id){
        $to_id = Auth::user()->id;
        $allConvo = Message::select('*')->where([['from_id','=',$from_id],['to_id','=',$to_id]])->orWhere([['from_id','=',$to_id],['to_id','=',$from_id]])->orderby('created_at','asc')->get();
        DB::table('messages')->where([['from_id','=',$from_id],['to_id','=',$to_id],['seen',false]])->update(['seen' => true]);
        $totalNewMsgCount = Message::select('seen')->where([['to_id','=',$to_id],['seen',false]])->get()->count();
        return response()->json([$allConvo,$totalNewMsgCount]);
    }

    public function getNewMsgCount(){
        $id = Auth::user()->id;
        $count = Message::select('from_id')->where([['to_id','=',$id],['seen', false]])->get()->count();
        return response()->json($count);
    }
    
    public function renderProfile($userId){
        $totalThreads = Thread::select('*')->where('threads.user_id','=',$userId)->count();
        $totalReplies = Reply::select('*')->where('replies.user_id','=',$userId)->count();
        $email = User::select('email')->where('users.id','=',$userId)->get();
        $name = User::select('name')->where('users.id','=',$userId)->get();
        return view('profile', ['totalThreads' => $totalThreads, 'totalReplies'=>$totalReplies, 'email'=>$email, 'name' => $name]);
    }

    public function postMsg(Request $request){
        Log::info($request->getContent());
        $temp = new Message;
        $temp->from_id = Auth::user()->id;
        $temp->to_id = $request->input('to_id');
        $temp->msg = $request->input('msg');
        $temp->created_at = Carbon::now()->toDateTimeString();
        $result = $temp->save();
        return response()->json($result);
    }
}
