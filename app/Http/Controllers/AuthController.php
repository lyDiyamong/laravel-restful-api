<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends ApiController
{
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
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['name'] =  $user->name;
    
            return $this->successResponse([
                'access_token' => $success['token'],
                'token_type' => 'Bearer',
                'user' => $user
            ], 200);
        } 
        else{ 
            return $this->errorResponse('Unauthorised.', 401);
        } 
    }
}