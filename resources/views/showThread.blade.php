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
    <hr>
    <br>
    @if(isset($topComments))
        @foreach($topComments as $pp)
            @if($pp->parent == 0)
            <h4>{{$pp->reply}}--id:{{$pp->id}}-----parent:{{$pp->parent}}</h4>posted by {{$pp->name}}----------
             <a href="#" onclick= @auth "displayreplybar({{$pp->id}})" @endauth>Reply</a>
                @if($pp->has_child)
                    <button onclick="loadComments({{$pp->id}})" id="loadCommentsBtn{{$pp->id}}">Load Comments </button>
                @endif
            @endif
                <div id="reply{{$pp->id}}"></div>
                <div id="childReplies{{$pp->id}}"></div><hr>
        @endforeach
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
    @auth
    if(document.getElementById('reply'+parent).innerHTML==""){
    document.getElementById('reply'+parent).innerHTML='<form action="/postReply" method="POST">\
                @csrf\
                <input type="hidden" name="parent" value="'+parent+'">\
                <pre>           <textarea name="reply" class="col-sm-6" required autofocus></textarea>\
                <input type="submit" value="Post"></pre>\
            </form>';}
            else{
                document.getElementById('reply'+parent).innerHTML="";
            }
    @else
            alert('Please login!');
    @endauth
}
function loadComments(parent){
    console.log('loadComments called with parent as ', parent);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     console.log(this.responseText);
     var p = JSON.parse(this.response);
     console.log(p.length);
     var allReplies = "";
     var level = getCommentLevel('childReplies'+parent);
     level++;
     for(var i = 0; i < p.length; i++){
        for(var j = 0; j< level;j++){
            allReplies += "----";
        }
        allReplies += p[i].reply + "----posted by: "+ p[i].name+ "  id:" + p[i].id;
        if(p[i].has_child){
            allReplies +=  "<button onclick='loadComments("+p[i].id+")' id='loadCommentsBtn"+p[i].id +"'>Load Comments </button></div>"
        }
        allReplies += "<a href='#' onclick= 'displayreplybar("+p[i].id+")'>Reply</a>"
        allReplies += "<div id='reply"+p[i].id+"'></div>";
        allReplies += "<div id='childReplies"+p[i].id+"'></div>";
     }
     console.log('all replies are',allReplies);
     console.log('the div name for comments container be: childReplies'+parent);
     document.getElementById('childReplies'+parent).innerHTML = allReplies;
     document.getElementById('loadCommentsBtn'+parent).style.visibility = "hidden";
    }
  };
  xhttp.open("GET", "/getChildReplies/"+parent, true);
  xhttp.send();
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
</script>
@endsection