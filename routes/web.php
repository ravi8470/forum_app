<?php

use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@renderHome');
Auth::routes();

Route::get('/create_thread',function(){
    return view('create_thread');
})->name('create_thread');

Route::post('/create_thread', 'ThreadController@create_thread');
