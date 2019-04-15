@extends('layouts.app')
@section('assets')
    <link rel="stylesheet" href="{{asset('/css/inbox.css')}}">
@endsection
@section('moreNavBarItems')
    <li class="nav-item">
        <a data-toggle="modal" data-target="#myModal" class="nav-link">SearchUser</a>
    </li>
    <div class="modal fade" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <div id="inputBoxContainer">
                    <input type="text" onkeyup="searchUsers(this.value)" style="width:100%" placeholder="enter a user's name">
                    <div id="userSearchResults"></div>
                </div>    
                
                </div>
            </div>
        </div>     
    </div>
@endsection
@section('content')
<div class="wrapper">
    <div class="box1" id="leftCol">
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
    <div class="box2">
        <div class="nested">
            <div class="convoBox" id="convoContainer"></div>
            <div class="sendBox">
                <textarea name="typeMsgBox" id="typeMsgBox" class="inboxTextArea" style="height:30px;"></textarea>
            </div>
            <div class="sendBtnBox">
                <button id= "sendMsgButton" value="Send" onclick="sendMsgFromInbox()" minlength="2">Send</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    window.onload = disableSendBtn();
    function disableSendBtn(){
        document.getElementById('sendMsgButton').disabled = true;
    }
    function sendMsgFromInbox(){
        document.getElementById('sendMsgButton').disabled = true;
        var msg = document.getElementById('typeMsgBox').value;
        if(msg.length < 2){
            document.getElementById('sendMsgButton').disabled = false;
            return;
        }
        document.getElementById('typeMsgBox').value = "";
        var to_id = window.lastFromId;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if(this.response){
                    document.getElementById('convoContainer').innerHTML += "<p class='alignTextRight rightPane '><span class='rightSpan'>"+msg+"</span></p>";
                    document.getElementById('typeMsgBox').value = "";
                }
            }
            document.getElementById('sendMsgButton').disabled = false;
        }
        xhttp.open("POST","/postMsg", true);
        xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xhttp.send("to_id="+to_id+"&msg="+msg);   
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

    function searchUsers(searchTerm){
        document.getElementById('userSearchResults').innerHTML = "";
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var x = JSON.parse(this.response);
                var content = "";
                function populateSearch(item,index){
                    content += "<p onclick=\"loadConvo('"+item.name+"',"+item.id+")\">"+item.name+"</p><hr>"
                }
                x.forEach(populateSearch);
                document.getElementById('userSearchResults').innerHTML = content;
            }       
        }
        xhttp.open('GET','/searchUsers/'+searchTerm, true);
        xhttp.send();
    }

    function loadConvo(name,id){
        $('#myModal').modal('hide');
        if(document.getElementById('sender'+id)){
            getConvo(id,name);
        }
        else{
            if(id != {{Auth::user()->id}}){
                var x = document.createElement('p');
                x.id = "sender"+id;
                x.className = "leftPane";
                x.addEventListener("click", function(){
                        getConvo(id,name);
                    });
                x.appendChild(document.createTextNode(name));
                document.getElementById('leftCol').appendChild(x);
                document.getElementById('leftCol').appendChild(document.createElement('hr'));
                getConvo(id,name);
            }
        }
    }
</script>
@endsection