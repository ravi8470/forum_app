@extends('layouts.app')
@section('content')
@if(isset($saved) && $saved == true)
    <div class="alert alert-success alert-dismissible col-sm-6">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong>Your Thread was Posted Successfully</strong>
    </div>
@endif

@if(isset($sections))
    <ul>
    @foreach ($sections as $section => $arr)
        <li>{{$section}}</li>
        <ol>
        @foreach ($arr as $x)
            <a href="#"><li>{{$x}}</li></a>
        @endforeach    
        </ol>
    @endforeach
    </ul>
@endif
@endsection