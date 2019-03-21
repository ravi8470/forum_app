<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-signin-client_id" content="664887441740-epd8mikfl872l1393l14v7o41e94pq7h.apps.googleusercontent.com">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Tech Forum</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{asset('/css/app.css') }}" rel="stylesheet">
    @yield('assets')   
    
</head>
<body>
    <div id="app">
<nav class="navbar navbar-expand-md navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">Tech Forum</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                    <li class="nav-item">
                            <a href="{{route('googleLogin')}}" class="nav-link">GLogin</a>
                    </li>   
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="/profile/{{Auth::user()->id}}">Profile</a>
                        <a class="dropdown-item" href="{{route('createThread')}}">Create Thread</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                        </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                    <!-- <li class="nav-item">
                        <a href="{{route('createThread')}}" class="nav-link">Create Thread</a>
                    </li> -->
                    <li class="nav-item">
                        <a href="{{ route('inbox') }}" class="nav-link" id="InboxBtn">Inbox</a>
                    </li>
                    @yield('moreNavBarItems')
                @endguest
            </ul>
        </div>
    </div>
</nav>
{{-- <div id="googleSigninStuff"></div>
<img  id='profilepic' src="" width="100px" height="100px"/> --}}
        <main class="">
            @yield('content')
        </main>
    </div>
<script>
    @auth
        window.onload = getNewMsgCount();
    @endauth
    // function onSignIn(verifiedGoogleUser){
    //     var profile = verifiedGoogleUser.getBasicProfile();
    //     var email = profile.getEmail();
    //     document.getElementById('profilepic').src = profile.getImageUrl();
    //     document.getElementById('googleSigninStuff').innerHTML = email;
    // }
    function getNewMsgCount(){
        console.log('getnewmsg count called');
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.response);
                if(this.response > 0){
                    document.getElementById('InboxBtn').innerHTML = 'Inbox('+this.response+')';
                    document.getElementById('InboxBtn').style.color = 'Green';
                }
            }
        }
        xhttp.open('GET', '/getNewMsgCount' , true);
        xhttp.send();
    }
</script>
<script src="https://apis.google.com/js/platform.js" async ></script>
<script src="{{ asset('js/app.js') }}" ></script>
@yield('scripts')
</body>
</html>
