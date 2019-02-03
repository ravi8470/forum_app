<?php

use App\Reply;

function hasChildComments($parent, $topComments){
    foreach($topComments as $x){
        if($x->parent == $parent)
            return true;
    }
    return false;
}
// function getChildReplies(){
//     $x = Reply::select('reply')->where('parent','=',1);
//     return $x;
// }