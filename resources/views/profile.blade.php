@extends('layouts.app')

@section('content')
@php
     preg_match('#/profile/([0-9]+)#', Request::url(), $match);
@endphp    
<div id="msgStatus"></div>
<div class="container" style="max-width:500px">
    <div class="card">
        <img class="card-img-top" src="{{asset('img/img_avatar1.png') }}" >
        {{-- substr value to be changed before pushing to heroku  --}}
        <div class="card-body">
            <h4 class="card-title">{{$name[0]->name ?? NULL}}</h4>
            <h4 class="card-title">{{$email[0]->email ?? NULL}}</h4>
            <p class="card-text">Total Threads: {{$totalThreads ?? NULL}}</p>
            <p class="card-text">Total Replies: {{$totalReplies ?? NULL}}</p>
            @auth
                @if(Auth::user()->id != $match[1])
                    <span id="msgContainer" ><a class="btn btn-primary" style="vertical-align: middle" onclick="displaySendMsgBox()">Send Message</a></span>
                @endif
            @else
                Please <a href="{{route('login')}}">Login</a> to send a msg.
            @endauth
        </div>
    </div>
    <div ></div>
</div>
<script>
    function displaySendMsgBox(){
        document.getElementById('msgContainer').innerHTML = '\
        <input type="textarea" id="msgBox" style="width:75%; margin-left:7%" maxlength="20" required minlength="2" name="msg">\
        <button onclick="postMsg('+@auth{{Auth::user()->id}}@endauth+ ',' + {{$match[1]}} +  ')"> Send</button>\
        </form>';
    }


    function postMsg(from_id,to_id){
        console.log('from and to:',from_id,to_id);
        console.log(document.getElementById('msgBox').value);
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                    var result = JSON.parse(this.response);
                    if(result == true){
                        document.getElementById('msgStatus').innerHTML = '<div class="alert alert-success alert-dismissible col-sm-6">\
                        <a href="#" class="close" data-dismiss="alert">&times;</a>\
                        <strong>Your message was sent successfully.</strong></div>'
                    }
                    else{
                        document.getElementById('msgStatus').innerHTML = '<div class="alert alert-danger alert-dismissible col-sm-6">\
                        <a href="#" class="close" data-dismiss="alert">&times;</a>\
                        <strong>Error in sending message!!!</strong></div>'
                    }
                    document.getElementById('msgContainer').innerHTML =   '<div id="msgContainer"><a class="btn btn-primary" style="margin-left: 38%" onclick="displaySendMsgBox()">Send Message</a></div>'; 
            }
        }
        xhttp.open("POST","/postMsg", true);
        xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xhttp.send("from_id="+from_id+"&to_id="+to_id+"&msg="+document.getElementById('msgBox').value);
    }
</script> 
@endsection