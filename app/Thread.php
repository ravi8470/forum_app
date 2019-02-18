<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    public static function store(Request $request){
        $temp = new Thread;
        $temp->user_id = Auth::user()->id;
        $temp->title = $request->input('title');
        $temp->body = $request->input('body');
        $temp->section = $request->input('section');
        if($temp->save()){
            return Thread::select('id')->where([['title','=',$request->input('title')],['body','=',$request->input('body')]])->get();
        }
        return false;
    }
    public static function getSections(){
        $sections = Thread::select('section')->distinct()->orderby('section','asc')->get()->pluck('section');
        $pp = array();
        foreach($sections as $sec){
            $pp[$sec] = Thread::select('title','id')->where('section','=',$sec)->limit(50)->get();
            // Log::debug($pp[$sec]);
        }
        foreach($pp as $sec){
            foreach($sec as $x){
                $x->count = Reply::select('replies.id')->where('replies.thread_id','=',$x->id)->count();
                // dump($x);
            }
        }
        // $pp['Section1'][0]->count = 6;
        // dd($pp['Section1'][0]->count);    
        return $pp;
    }
}
