<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Input;

class Token extends Model
{
    //
    public static function getUser(){
    	$data = Input::all();
    	$token = Token::whereAccessToken($data['access_token'])->first();
    	$user = User::find($token->user_id);
    	return $user;
    }
}
