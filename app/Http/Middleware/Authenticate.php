<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data = $request->all();
        $rules =[
            'access_token'   => 'required'
            // 'gcm_id'         => 'required'
        ];
        $validation = Validator::make($data,$rules);
        $message = [
            'error' => [
                'message' => 'access_token parameter required',
                'status_code' => 404
                // 'expires_in' => $token->expires.' minutes'
            ]
        ];
        if($validation->fails()){
            return $this->response->setStatusCode(404)->withArray($message);
        }
        $token = Token::whereAccessToken($data['access_token'])->first();
        if($token == null){
            $message = [
                'error' => [
                    'message' => 'invalid access_token',
                    'status_code' => 400
                    // 'expires_in' => $token->expires.' minutes'
                ]
            ];
            return $this->response->setStatusCode(400)->withArray($message);
        }
        $token_created_date = Carbon::parse($token->updated_at);
        if($token_created_date->diffInMinutes() > Meta::find(1)->value){
            $message = [
                'error' => [
                    'message' => 'access_token validity expires',
                    'status_code' => 401
                    // 'expires_in' => $token->expires.' minutes'
                ]
            ];
            return $this->response->setStatusCode(401)->withArray($message);
        }
        return $next($request);
    }
}
