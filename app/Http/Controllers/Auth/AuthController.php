<?php

namespace App\Http\Controllers\Auth;

use EllipseSynergie\ApiResponse\Contracts\Response;
use Illuminate\Http\Request;

// Models
use App\User;
use App\Token;
use App\Meta;


use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(Response $response)
    {
        $this->middleware('guest', ['except' => 'getLogout']);
        $this->response = $response;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function getAccessToken(Request $request)
    {
        $data = $request->all();
        $rules =[
            'uid'                  => 'required',
            // 'gcm_id'               => 'required'
        ];
        $validation = Validator::make($data,$rules);
        if($validation->fails()){
            return $this->response->setStatusCode(400)->withArray(['error' => $validation->messages()]);
        }
        $user = User::whereFacebookUserId($data['uid'])->first();
        if($user){
            $access_token = md5($user->facebook_user_id.time().rand(111111,9099999));
            $refresh_token = md5(time().$user->facebook_user_id.rand(111111,9099999).'refresh');
            $token = new Token;
            $token->access_token = $access_token;
            $token->refresh_token = $refresh_token;
            $token->expires = Meta::find(1)->value;
            $token->user_id = $user->id;
            $token->save();

            // $device = Device::whereGcmId($data['gcm_id'])->first();
            // if($device == null){
            //     $device = new Device;
            //     $device->gcm_id = $data['gcm_id'];
            // }
            // $device->user_id = $user->id;
            // $device->save();
            $data = [
                'data' => [
                    'access_token' => $access_token,
                    'refresh_token' => $refresh_token,
                    'expires_in' => $token->expires.' minutes'
                ]
            ];
            return $this->response->withArray($data);
        }
        return $this->response->setStatusCode(400)->withArray(['error' => 'Invalid User']);
    }
    public function refreshToken(Request $request){
        $data = $request->all();
        $rules =[
            'refresh_token'                  => 'required'
            // 'gcm_id'               => 'required'
        ];
        $validation = Validator::make($data,$rules);
        if($validation->fails()){
            return $this->response->setStatusCode(400)->withArray(['error' => $validation->messages()]);
        }
        $token = Token::whereRefreshToken($data['refresh_token'])->first();
        if($token == null){
            $data = [
                'error' => [
                    'message' => 'Invalid Refresh Token',
                    'status_code' => 401
                    // 'expires_in' => $token->expires.' minutes'
                ]
            ];
            return $this->response->setStatusCode(401)->withArray($data);
        }
        $access_token = md5($token->user_id.time().rand(111111,9099999));
        $refresh_token = md5(time().$token->user_id.rand(111111,9099999).'refresh');
        $token->access_token = $access_token;
        $token->refresh_token = $refresh_token;
        $token->save();
        $data = [
            'data' => [
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
                'expires_in' => $token->expires.' minutes'
            ]
        ];
        return $this->response->withArray($data);
    }
}
