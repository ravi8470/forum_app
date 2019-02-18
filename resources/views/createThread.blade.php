@extends('layouts.app')

@section('content')

@if (Auth::check())
    <h3>Create a thread</h3>
    <form action="/createThread" method="POST">
        @csrf
        <label class="col-sm-1">Title:</label><input type="text" name="title" minlength="10" required class="col-sm-6"><br><br>
        <label class="col-sm-1">Body:</label><textarea name="body" minlength="10" class="col-sm-6" required></textarea><br><br>
        <label class="col-sm-1">Section</label>
        @if(isset($allSections))
            <select name="section" class="col-sm-4">
                @foreach ($allSections as $item)
                    <option>{{$item}}</option>
                @endforeach
            </select>
        @endif
        <br><br>
        <input type="submit" value="submit" class="col-sm-4">    
    </form>
@else 
    Please <a href="{{ route('login') }}">Login</a> to create a thread.
@endif
@endsection
