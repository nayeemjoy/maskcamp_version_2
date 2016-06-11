<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'api/v1'], function(){
	// Route::controller('password', 'RemindersController');
	Route::post('register', ['as'=>'user.store','uses' => 'UserController@store']);
	Route::post('access_token', ['as'=>'access_token','uses' => 'Auth\AuthController@getAccessToken']);
	Route::post('refresh_token', ['as'=>'refresh_token','uses' => 'Auth\AuthController@refreshToken']);


	Route::group(array('middleware' => 'auth'), function()
	{


		Route::get('posts', ['as'=>'post.index','uses' => 'PostController@index']);


		
	});
});





Route::get('socket', 'SocketController@index');
Route::post('sendmessage', 'SocketController@sendMessage');
Route::get('writemessage', 'SocketController@writemessage');