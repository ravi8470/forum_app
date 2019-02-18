@extends('layouts.app')

@section('content')
    @if($savedReply ?? false)
        <div class="alert alert-success alert-dismissible col-sm-6">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>Your reply was Posted Successfully</strong>
        </div>
    @elseif(isset($savedReply))
        <div class="alert alert-danger alert-dismissible col-sm-6">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>Error in posting reply.</strong>
        </div>
    @endif
    @if(isset($data)&& isset($userName))
        @foreach($data as $x)
            <div class="card col-md-6" >
                <div class="card-header">{{$x->title}}</div>
                <div class="card-body">{{$x->body}}</div>                
            </div>
           <span class="align-content-end"> Posted by {{$userName}}</span>
        @endforeach
    @endif
    <hr><br>
    {{-- @if(isset($topComments))
        @foreach($topComments as $pp)
            @if($pp->parent == 0)
            <h4>{{$pp->reply}}--id:{{$pp->id}}-----parent:{{$pp->parent}}</h4>posted by {{$pp->name}}----------
             <button onclick=  displayreplybar({{$pp->id}})>Reply</button>
                @if($pp->has_child)
                    <button onclick="loadComments({{$pp->id}})" id="loadCommentsBtn{{$pp->id}}">Load Comments </button>
                @endif
            @endif
                <div id="reply{{$pp->id}}"></div>
                <div id="childReplies{{$pp->id}}" class="comments"></div><hr>
        @endforeach
    @endif --}}
    <h3>Comments:</h3>
    <div id="cc"></div>
    @if(isset($numPages) && $numPages > 1 )
        @for ($i = 0; $i < $numPages && $i < 6; $i++)
            <button onclick="loadCommentsViaJs({{$i}})">{{$i+1}}</button>
        @endfor
    @endif
    
        @if(isset($numPages) && $numPages > 1)
            .. Jump to Page: <select>
            @for ($i = 0; $i < $numPages && $i < 6; $i++)
                <option onclick="loadCommentsViaJs({{$i}})">{{$i+1}}</option>
            @endfor
            </select><br>
        @endif
    
    @auth
            Post a Reply:
            <form action="/postReply" method="POST">
                @csrf
                <input type="hidden" name="parent" value="0">
                <textarea name="reply" class="col-sm-6" required></textarea>
                <input type="submit" value="Post">
            </form>
    @else
            Please <a href="{{route('login')}}">Login </a> to post a reply.
    @endauth
            
<script>
function displayreplybar(parent){
    console.log('displayreplybar for id:', parent);
    if(document.getElementById('replyBox'+parent).innerHTML==""){
    console.log('hgjhgjhg'+parent);
    document.getElementById('replyBox'+parent).innerHTML='<form action="/postReply" method="POST">\
                @csrf\
                <input type="hidden" name="parent" value="'+parent+'">\
                 <textarea name="reply" class="col-sm-6" required></textarea>\
                <input type="submit" value="Post">\
            </form>';}

      else{
                console.log("hyyuyu");
                document.getElementById('replyBox'+parent).innerHTML="";
            }
}


function getCommentLevel(divId){
    var ctr = 0;
    var x = document.getElementById(divId);
    while(x.parentNode.id.match(/childReplies/)){
        ctr++;
        x = x.parentNode;
        console.log('cureent node is',x,'ctr is ',ctr);
    }
    console.log('final ctr value:',ctr);
    return ctr;
}
window.onload = loadCommentsViaJs(0);

function loadCommentsViaJs(offset){
    // console.log(window.location.pathname);
    console.log('loadcomm called with: ',offset); 
    document.getElementById('cc').innerHTML = "";
    var p = window.location.pathname;
    var x = '/getTreeAsJson/'+p.substr(12)+'/'+offset;
    console.log(x);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        var flatArray = JSON.parse(this.response);
        console.log(flatArray);
        for(var i = 0; i < flatArray.length; i++){
            var div = document.createElement('div');
            var userNameText = document.createTextNode(flatArray[i].name.toUpperCase());
            var tnode = document.createTextNode(flatArray[i].reply);
            var replybtn = document.createElement('a');
            replybtn.appendChild(document.createTextNode('Reply'));
            // replybtn.className = 'btn-primary col-sm-1 ';
            div.appendChild(userNameText);
            div.appendChild(document.createElement('br'));
            div.appendChild(tnode);
            var spanForReplyAndExpand = document.createElement('span');
            div.appendChild(spanForReplyAndExpand);
            spanForReplyAndExpand.appendChild(replybtn);
            if(flatArray[i].has_child){
                var expandCommentsBtn = document.createElement('a');
                expandCommentsBtn.appendChild(document.createTextNode(' Expand'));
                expandCommentsBtn.id = "expandComments"+flatArray[i].id;
                expandCommentsBtn.onclick = function (parent){
                    return function(){
                        var x = document.getElementById('comment'+parent).childNodes;
                        if(document.getElementById('expandComments'+parent).innerHTML == ' Expand'){
                            for(var i = 0; i < x.length; i++){
                                if(x[i].className && x[i].className.match(/hideC/)){
                                    x[i].className = "card card-body comments";
                                }
                            }
                            document.getElementById('expandComments'+parent).innerHTML = ' Collapse';
                        }
                        else{
                            for(var i = 0; i < x.length; i++){
                                if(x[i].className && x[i].className.match(/comments/)){
                                    x[i].className = "card card-body comments hideC";
                                }
                            }
                            document.getElementById('expandComments'+parent).innerHTML = ' Expand';
                        }
                    }
                }(flatArray[i].id);
                spanForReplyAndExpand.appendChild(expandCommentsBtn);
            }
            div.id = 'comment'+flatArray[i].id;
            div.className = "card card-body comments ";
            var replyBox = document.createElement('div');
            replyBox.id = 'replyBox'+flatArray[i].id;
            div.appendChild(replyBox);
            var temp = flatArray[i].id;
            
            if(flatArray[i].parent == 0){
                document.getElementById('cc').appendChild(div);
            }
            else{
                document.getElementById('comment'+flatArray[i].parent).appendChild(div);
                div.className += "hideC";
            }
            replybtn.onclick = function (parent){
                return function (){
                    var tempNode = document.getElementById('replyBox'+parent);
                    if(tempNode.style.display == 'block'){
                        tempNode.style.display = 'none';
                        return;
                    }
                    else{
                        @auth
                        document.getElementById('replyBox'+parent).innerHTML='<form action="/postReply" method="POST">\
                        @csrf\
                        <input type="hidden" name="parent" value="'+parent+'">\
                        <textarea name="reply" class="col-sm-6" required></textarea>\
                        <input type="submit" value="Post">\
                        </form>';
                        @else
                        document.getElementById('replyBox'+parent).innerHTML="please <a href='{{route("login")}}'>Login </a>";
                        @endauth
                        document.getElementById('replyBox'+parent).style.display = 'block';
                    }
                }
            }(flatArray[i].id);
        }
    }
}
    xhttp.open('GET', x , true);
    xhttp.send();    
}
</script>
@endsection