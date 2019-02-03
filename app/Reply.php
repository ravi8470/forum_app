<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    public static function storeReply(Request $request){
        $temp = new Reply;
        $temp->user_id = Auth::user()->id;
        if(session('currThreadId') == NULL)
            return false;         
        $temp->thread_id = session('currThreadId');
        $temp->reply = $request->input('reply');
        $temp->parent = $request->input('parent');
        if($temp->parent != 0){
            Reply::where('id','=',$temp->parent)->update(['has_child' => true]);
        }
        return $temp->save();
    }
}
