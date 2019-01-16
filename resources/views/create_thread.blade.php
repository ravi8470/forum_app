@extends('layouts.app')

@section('content')

@if (Auth::check())
    <h3>Create a thread</h3>
    <form action="/create_thread" method="POST">
        @csrf
        <label class="col-sm-1">Title:</label><input type="text" name="title" minlength="10" required class="col-sm-6"><br><br>
        <label class="col-sm-1">Body:</label><textarea name="body" minlength="10" class="col-sm-6" required></textarea><br><br>
        <label class="col-sm-1">Section</label>
        <select name="section" class="col-sm-4">
            <option>Section1</option>
            <option>Section2</option>
            <option>Section3</option>
            <option>Section4</option>
            <option>Section5</option>
        </select><br><br>
        <input type="submit" value="submit" class="col-sm-4">    
    </form>
@else 
    Please <a href="{{ route('login') }}">Login</a> to create a thread.
@endif
@endsection
