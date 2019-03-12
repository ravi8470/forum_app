@extends('layouts.app')
@section('assets')
    <link rel="stylesheet" href="{{asset('/css/inbox.css')}}">
@endsection
@section('content')
    <div class="split left">
        @if(isset($uniqueSenders) && isset($newMsgCountPerUser))
            <hr>
            @foreach ($uniqueSenders as $key => $x)
                <p onclick="getConvo({{$x}},'{{$key}}')" class="leftPane" id="sender{{$x}}">{{$key}}
                    @if($newMsgCountPerUser[$x] > 0)
                        ({{$newMsgCountPerUser[$x]}})
                    @endif
                </p><hr>
            @endforeach
        @endif
    </div>
    <div class="split right">
        <div class="convoContainer" id="convoContainer"></div>
        <div class="sendMsgBox">
            <textarea name="typeMsgBox" id="typeMsgBox" class="typeMsgBox" required minlength="2"></textarea>
            <button id= "sendMsgButton" class="sendMsgButton btn btn-primary" value="Send" onclick="sendMsgFromInbox()">Send</button>
        </div>
    </div>
    
{{-- @if(isset($newMsgCount) && isset($newMsg))
    <h3>New Messages:</h3>
    <div class="table-responsive">
        <table class="table">
            <th>From</th>
            <th>Message:</th>
            <th>Sent</th>
        @foreach($newMsg as $msg)
            <tr>
            <td>{{$msg->name}}</td>
            <td>{{$msg->msg}}</td>
            <td>{{$msg->created_at}}</td>
            </tr>
        @endforeach
        </table>
    </div>
@endif --}}
{{-- @if(isset($allMsg))
    <h3>All Messages:</h3>
    <div class="table-responsive">
        <table class="table">
            <th>From</th>
            <th>Message:</th>
            <th>Sent</th>
        @foreach($allMsg as $msg)
            <tr>
            <td>{{$msg->name}}</td>
            <td>{{$msg->msg}}</td>
            <td>{{$msg->created_at}}</td>
            </tr>
        @endforeach
        </table>
    </div>
@endif --}}
<script>

        var pusher = new Pusher("f6f6bb651131cd2d8ee0", {
            cluster: "ap2"
        });
        var channel = pusher.subscribe("MessageToFrom");
        channel.bind("newMessage", function(data) {
            alert("An event was triggered with message: " + data.message);
        });

    window.onload = disableSendBtn();
    function disableSendBtn(){
        document.getElementById('sendMsgButton').disabled = true;
    }
    function sendMsgFromInbox(){
        var msg = document.getElementById('typeMsgBox').value;
        var to_id = window.lastFromId;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if(this.response){
                    document.getElementById('convoContainer').innerHTML += "<p class='alignTextRight rightPane '><span class='rightSpan'>"+msg+"</span></p>";
                    document.getElementById('typeMsgBox').value = "";
                }
            }
        }
        xhttp.open("POST","/postMsg", true);
        xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xhttp.send("to_id="+to_id+"&msg="+document.getElementById('typeMsgBox').value);   
    }
    function getConvo(from_id, name){
        //sets the last user for which the conversation was pulled from server.
        if(window.lastFromId){
            document.getElementById('sender'+window.lastFromId).className = "leftPane";
        }
        window.lastFromId = from_id;
        document.getElementById('sendMsgButton').disabled = false;
        document.getElementById('sender'+from_id).className += " highlightName";
        document.getElementById('convoContainer').innerHTML = "";
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.response);
                var x = JSON.parse(this.response);
                // console.log(x);
                for(var i = 0; i < x[0].length; i++){
                    if(x[0][i].from_id == from_id){
                        document.getElementById('convoContainer').innerHTML += "<p class='alignTextLeft rightPane '><span class='leftSpan'>"+x[0][i].msg+"</span></p>";
                    }
                    else{
                        document.getElementById('convoContainer').innerHTML += "<p class='alignTextRight rightPane '><span class='rightSpan'>"+x[0][i].msg+"</span></p>";
                        // document.getElementById('convoContainer').innerHTML += "<p class='alignTextRight rightPane  rightSpan'>"+x[i].msg+"</p>";
                    }
                }
                if(x[1] > 0)
                    document.getElementById('InboxBtn').innerHTML = "Inbox(" + x[1] + ")";
                else{
                    document.getElementById('InboxBtn').innerHTML = "Inbox";    
                    document.getElementById('InboxBtn').style.color = 'Black';
                }
                document.getElementById('sender'+from_id).innerHTML = name;
            }
        }
        xhttp.open('GET', '/getConvo/'+from_id , true);
        xhttp.send();
    }
</script>
@endsection