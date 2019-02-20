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

Route::get('/home','HomeController@renderHome');

Route::get('/profile/{userId}','UserController@renderProfile')->name('profile');

Route::post('/postMsg','UserController@postMsg');

Route::get('/inbox','UserController@renderInbox')->name('inbox')->middleware('auth');

Auth::routes();

Route::get('/createThread',function(){
    return view('createThread',['allSections'=>array('Sports', 'Education','Business','Chit Chat','Anything Else')]);
})->name('createThread')->middleware('auth');

Route::post('/createThread', 'ThreadController@createThread');

Route::get('/showThread/{threadId}', 'ThreadController@showThread');

Route::post('/postReply', 'ReplyController@postReply');

Route::get('/getChildReplies/{parent}','ReplyController@getChildReplies');

Route::get('/getTreeAsJson/{threadId}/{offset}','ThreadController@getTreeAsJson');