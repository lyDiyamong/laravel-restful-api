<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;

use Illuminate\Http\Request;
use App\Libraries\IssueToken;
use App\Events\UserRegistered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use Laravel\Passport\Client as OClient;
use Illuminate\Support\Facades\Validator;

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

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->verified = User::UNVERIFIED_USER;
        $user->verification_token = User::generateVerificationCode();
        $user->token_expires = now()->addMinutes(10);
        $user->admin = User::REGULAR_USER;
        $user->save();

        return $this->successResponse([
            'message' => 'User registered successfully and please check your email for verification'
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $res = IssueToken::scope('*')
                ->usePasswordGrantType()
                ->issueToken($credentials);

            if (!$res->success) {
                return $this->errorResponse(
                    $res->json['message'] ?? 'Authentication failed',
                    400
                );
            }

            // Creating a Laravel response object
            $response = $this->successResponse([
                'data' => [
                    'access_token' => $res->access_token,
                ],
                "message" => "Login successfully"
            ]);

            // Add refresh token to HTTP-only cookie if it exists
            if (isset($res->refresh_token)) {
                $cookie = $this->setRefreshCookie('refresh_token', $res->refresh_token);
                return $response->withCookie($cookie);
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
            ->issueToken();

        if (!$res->success) {
            return $this->errorResponse(
                'The refresh token is invalid.',
                400
            );
        }

        // Create response with new tokens
        $response = $this->successResponse([
            'data' => [
                'access_token' => $res->access_token,
            ],
            "message" => "Refresh token successfully"
        ]);

        // Set a new refresh token cookie if one is returned
        if (isset($res->refresh_token)) {
            $cookie = $this->setRefreshCookie('refresh_token', $res->refresh_token);
            return $response->withCookie($cookie);
        }

        return $response;
    }

    public function logout(Request $req)
    {
        $token = $req->user()->token();

        if (!$token) {
            return $this->errorResponse("Token not found", 400);
        }

        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $token->id)
            ->update(['revoked' => true]);

        $token->revoke();

        return $this->successResponse([
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $req)
    {
        $user = $req->user();
        return $this->successResponse([
            'data' => $user
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $user = User::where('email', $request->email)
            ->where('verification_token', $request->verification_token)
            ->where('token_expires', '>', now())
            ->first();

        if (!$user) {
            return $this->errorResponse("User not found", 404);
        }
        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;
        $user->save();
        return $this->successResponse([
            'message' => 'User verified successfully'
        ]);
    }

    public function resendOtp(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        $user->verification_token = User::generateVerificationCode();
        $user->save();
        event(new UserRegistered($user));
        return $this->successResponse([
            'message' => 'Otp sent successfully'
        ]);
    }
}
