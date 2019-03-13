<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;

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
Auth::routes();

Route::get('/googleLogin','Auth\LoginController@redirectToProvider')->name('googleLogin');

Route::get('/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('/', 'HomeController@renderHome')->name('home');

Route::get('/home','HomeController@renderHome');

Route::get('/profile/{userId}','UserController@renderProfile')->name('profile');

Route::post('/postMsg','UserController@postMsg');

Route::get('/inbox','UserController@renderInbox')->name('inbox')->middleware('auth');

Route::get('/getNewMsgCount','UserController@getNewMsgCount')->middleware('auth');

Route::get('/getConvo/{from_id}','UserController@getConvo');

Route::get('/createThread',function(){
    return view('createThread',['allSections'=>array('Sports', 'Education','Business','Chit Chat','Anything Else')]);
})->name('createThread')->middleware('auth');

Route::post('/createThread', 'ThreadController@createThread');

Route::get('/showThread/{threadId}', 'ThreadController@showThread');

Route::post('/postReply', 'ReplyController@postReply');

// Route::get('/getChildReplies/{parent}','ReplyController@getChildReplies');

Route::get('/getTreeAsJson/{threadId}/{offset}','ThreadController@getTreeAsJson');