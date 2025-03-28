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
use App\Libraries\IssueToken;

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

        User::create($data);

        // For API authentication, we'll use Passport
        $res = IssueToken::scope('*')
            ->usePasswordGrantType()
            ->issueToken($request);

        return $this->successResponse([
            'token' => $res->json,
            'message' => 'User registered successfully'
        ], 201);


    }
    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $res = IssueToken::scope('*')
            ->usePasswordGrantType()
            ->issueToken($request);

        if (!$res->success) {
            return $this->errorResponse(
                $res->json['message'] ?? 'Authentication failed',
                400
            );
        }

        // Creating a Laravel response object
        $response = $this->successResponse([
            'data' => $res->json
        ]);

        // Add refresh token to HTTP-only cookie if it exists
        if (isset($res->json['refresh_token'])) {
            return $response->cookie(
                'refresh_token', 
                $res->json['refresh_token'], 
                60 * 24 * 30, // Expires in 30 days
                '/', // Path
                null, // Domain (null = default)
                false, // Secure (set to true in production with HTTPS)
                true // HTTP-only (prevents JavaScript access)
            );
        }

        return $response;
    }

    return $this->errorResponse("Incorrect password or email", 403);
}


    public function refreshToken(Request $request)
    {
        $refreshToken = $request->cookie('refresh_token');

        if (!$refreshToken) {
            return $this->errorResponse("Refresh token not found", 400);
        }

        // Create a modified request with the refresh token
        $request->merge(['refresh_token' => $refreshToken]);

        $res = IssueToken::scope('*')
            ->useRefreshTokenGrantType()
            ->issueToken($request);

        if (!$res->success) {
            return $this->errorResponse(
                $res->json['message'] ?? 'The refresh token is invalid.',
                400
            );
        }

        // Create response with new tokens
        $response = $this->successResponse([
            'token' => $res->json
        ]);

        // Set a new refresh token cookie if one is returned
        if (isset($res->json['refresh_token'])) {
            $response->cookie(
                'refresh_token', 
                $res->json['refresh_token'], 
                60 * 24 * 30, // Expires in 30 days
                '/', // Path
                null, // Domain (null = default)
                false, // Secure (set to true in production with HTTPS)
                true // HTTP-only (prevents JavaScript access)
            );
        }

        return $response;
    }

    public function me()
    {
        
    }
}