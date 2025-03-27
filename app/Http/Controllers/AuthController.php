<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client as OClient;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class AuthController extends ApiController
{

    private $client;

    public function __construct()
    {
        $this->client = OClient::where("password_client", 1)->first();
    }
    //

    public function register(Request $request)
    {
        //
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        // $data['verification_token'] = User::generateVerificationCode();
        // $data['token_expires'] = Date::now()->addMinutes(10);
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);

        $token = $user->createToken('AuthToken')->accessToken;

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);


    }

        // LOGIN
    // public function login(Request $request)
    // {
    //     if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
    //         $user = Auth::user(); 
    //         $success['token'] =  $user->createToken('MyApp')-> accessToken; 
    //         $success['name'] =  $user->name;
    
    //         return $this->successResponse([
    //             'access_token' => $success['token'],
    //             'token_type' => 'Bearer',
    //             'user' => $user
    //         ], 200);
    //     } 
    //     else{ 
    //         return $this->errorResponse('Unauthorised.', 401);
    //     } 
    // }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $oClient = OClient::where('password_client', 1)->first();

            $http = new Client();

            Logger(url('/oauth/token'));


            // $response = $http->post(url('/oauth/token'), [
            //     'form_params' => [
            //         'grant_type' => 'password',
            //         'client_id' => $oClient->id,
            //         'client_secret' => $oClient->secret,
            //         'username' => $request->email,
            //         'password' => $request->password,
            //         'scope' => '',
            //     ]
            // ]);
            $params = [
                'grant_type' => 'password',
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '*',
            ];
            // dd($oClient);
            $request->request->add($params);
            $request->headers->set("Accept", "application/json");
            $request->headers->set("Content-Type", "application/json");


            $res = Request::create('/oauth/token', 'POST', $params);

            $res = Route::dispatch($res);

            // dd($res->getContent());


           return $this->successResponse([
            "json" => json_decode((string) $res->getContent(), true)
           ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function refreshToken(Request $request)
    {
        $oClient = OClient::where('password_client', 1)->first();

        $http = new Client();
        $response = $http->post(url('/oauth/token'), [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $request->refresh_token,
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'scope' => '',
            ]
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function me()
    {
        
    }
}