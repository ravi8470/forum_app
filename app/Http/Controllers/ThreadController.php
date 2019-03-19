<?php

namespace App\Http\Controllers;

use App\User;
use App\Reply;
use App\Thread;
use BlueM\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ThreadController extends Controller
{
    // public function showCreateThreadPage(){
    //     $allSections = array('Sports', 'Education','Business','Chit Chat','Anything Else');
    //     return view('createThread',['allSections'=>$allSections]);
    // }
    
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
        // $rootNodes = $tree->getRootNodes();
        // $rootNodesNum = count($tree->getRootNodes());
        // // dd($tree->getRootNodes()[6]->getChildren()[0]->getFollowingSibling());
        // for($x = 0; $x < $rootNodesNum; $x++){
        //     $temp = $rootNodes[$x];
        //     Log::debug('starting for new root**************************');
        //     while(is_object($temp)){
        //         Log::debug('showThread:::'.$temp->id);
        //         $tempx = ThreadController::printArm($temp);
        //         if($tempx->id != $rootNodes[$x]->id){
        //             $temp = ThreadController::getNextNode($tempx,$rootNodes[$x]);
        //         }
        //         else{
        //             Log::debug('enters else');
        //             break;
        //         }
                
        //     }    
        // }

        //dd($tree->getNodes()[8]->getDescendants());
        Log::debug('showthread called savedreply as '.session('savedReply').'and threadid as '.$threadId);
        $data = Thread::select('*')->where('id','=', $threadId)->get();
        Session::put('currThreadId' , ($data[0]->id ?? NULL));
        $userName = User::select('name')->where('id','=',$data[0]->user_id)->get()[0]->name;        
        $x = session('savedReply') ?? NULL;
            // $topComments = Reply::join('users','replies.user_id', '=', 'users.id')->select('users.name','replies.reply','replies.id','replies.has_child','replies.parent')->where([['replies.thread_id',
        //  '=',$data[0]->id],['replies.parent',0]])->orderby('replies.updated_at','desc')->limit(8)->get();
        
        session()->forget('savedReply');
        // $topComments = DB::select(DB::raw("WITH RECURSIVE childReplies AS( (SELECT replies.* FROM replies WHERE thread_id = :threadID AND replies.parent = 0 ORDER BY replies.id LIMIT 3 )
        // UNION
        // SELECT e.* FROM replies e INNER JOIN childReplies s ON s.id = e.parent) SELECT * FROM childReplies"),['threadID' => $data[0]->id]);
        // $pp = array();
        // $i = 0;
        // foreach($topComments as $a){
        //     $pp[$i] = (array)$a;
        //     $i++;
        // }
        // $ccc = new Tree($pp);
        // dd(response()->json($ccc->getNodes()));
        $allC = Reply::join('users','replies.user_id', '=', 'users.id')->select('users.name','replies.*',)->where('thread_id','=',$threadId)->get()->toArray();
        $tree = new Tree($allC);
        // dd($tree);
        // Log::debug('showthread() data and username and topcomments',['data'=> $data, 'usr'=>$userName, 'topc'=> $topComments]); 
        $numPages = Reply::select('*')->where([['thread_id','=',$threadId],['parent',0]])->count();
        $numPages = (int)ceil($numPages/6);
        return view('showThread',['data' => $data, 'userName'=> $userName, 'savedReply' =>$x,'numPages'=>$numPages]);
    }
    public static function printNode($temp){
        Log::debug('printNode::'.$temp->id);
        $x = $temp->getLevel();
        while($x--)
            echo '*';
        echo $temp->reply;
        echo "<br>";
        
    }
    public static function printArm($temp){
        Log::debug('printArm:::'.$temp->id);
        ThreadController::printNode($temp);
        while($temp->hasChildren()){
            Log::debug('printArm storing:::'.$temp->getChildren()[0]);
            $temp = $temp->getChildren()[0];
            Log::debug('printArm:::'.$temp->id);
            ThreadController::printNode($temp);
        }
        Log::debug('printArm returning::'.$temp->id);
        return $temp;
    }
    
    public static function getNextNode($temp, $root){
        while(1){
            Log::debug('getNextNode:::'.$temp->id);
            if(($sb = $temp->getFollowingSibling()) != NULL){            
                Log::debug('sb calculated:::'.$sb->id);
                return $sb;
            }
            else if($temp->getParent()->id == $root->id){
                Log::debug('returning false as parent hit?:::'.$temp->id);
                return false;
            }
            else{
                $temp = $temp->getParent();
                Log::debug("parent calculated::".$temp->id);
            }    
        }
    }
    public function getTreeAsJson($threadId, $offset){
        Log::debug('route hit getTreeAs Json with:',[$threadId,$offset]);
        // $allC = Reply::join('users','replies.user_id', '=', 'users.id')->select('users.name','replies.*',)->where('thread_id','=',$threadId)->get()->toArray();
        $allC = DB::select(DB::raw("WITH RECURSIVE childReplies AS( (SELECT replies.*, users.name FROM replies INNER JOIN users ON 
        replies.user_id = users.id WHERE thread_id = :threadID AND replies.parent = 0 ORDER BY replies.created_at DESC LIMIT 6 OFFSET :offset)
        UNION
        SELECT e.*, users.name FROM replies e INNER JOIN childReplies s ON s.id = e.parent INNER JOIN users ON 
        e.user_id = users.id) SELECT * FROM childReplies"),['threadID' => $threadId, 'offset' => $offset*6]);
        $temp = array();
        $i = 0;
        foreach($allC as $u){
            $temp[$i] = (array)$u;
            $i++;
        }
        $tree = new Tree($temp);
        // dd($allC);
        return response()->json($tree->getNodes());
    }
}