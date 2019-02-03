@extends('layouts.app')
@section('content')
@if($saved ?? false)
    <div class="alert alert-success alert-dismissible col-sm-6">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong>Your Thread was Posted Successfully</strong>
    </div>
@endif

@if($sections ?? false)
    <ul>
    @foreach ($sections as $section => $arr)
        <li>{{$section}}</li>
        <ul class="list-group col-sm-10">
        @foreach ($arr as $title =>$id)
            <a href="/showThread/{{$id->id}}"><li class="list-group-item">{{$id->title}}</li></a>
        @endforeach    
        </ul>
    @endforeach
    </ul>
@endif
@endsection