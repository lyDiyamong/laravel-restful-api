<?php

namespace App\Services;

use App\Models\User;
use App\Libraries\IssueToken;
use App\Events\UserRegistered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthService
{
    /**
     * Register a new user
     *
     * @param array $data Registration data including name, email, and password
     * @return array Response array with status and message
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'status' => 422
            ];
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'verified' => User::UNVERIFIED_USER,
            'verification_token' => User::generateVerificationCode(),
            'token_expires' => now()->addMinutes(10),
            'admin' => User::REGULAR_USER,
        ]);

        event(new UserRegistered($user));

        return [
            'success' => true,
            'message' => 'User registered successfully and please check your email for verification',
            'status' => 201
        ];
    }

    /**
     * Authenticate user and issue access token
     *
     * @param array $credentials User credentials (email and password)
     * @return array Response array with tokens and status
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Incorrect password or email',
                'status' => 403
            ];
        }

        $res = IssueToken::scope('*')
            ->usePasswordGrantType()
            ->issueToken($credentials);

        if (!$res->success) {
            return [
                'success' => false,
                'message' => $res->error_description ?? 'Authentication failed',
                'status' => 400
            ];
        }

        return [
            'success' => true,
            'data' => [
                'access_token' => $res->access_token,
                'refresh_token' => $res->refresh_token ?? null
            ],
            'message' => 'Login successfully',
            'status' => 200
        ];
    }

    /**
     * Refresh access token using refresh token
     *
     * @param string|null $refreshToken
     * @return array Response array with new tokens and status
     */
    public function refreshToken(?string $refreshToken): array
    {
        if (!$refreshToken) {
            return [
                'success' => false,
                'message' => 'Refresh token not found',
                'status' => 400
            ];
        }

        $res = IssueToken::scope('*')
            ->useRefreshTokenGrantType()
            ->issueToken(['refresh_token' => $refreshToken]);

        if (!$res->success) {
            $errorMessage = $this->getRefreshTokenError($res->error ?? '');

            return [
                'success' => false,
                'message' => $errorMessage,
                'status' => $res->error === 'invalid_grant' ? 401 : 400,
                'should_clear_cookie' => $res->error === 'invalid_grant'
            ];
        }

        return [
            'success' => true,
            'data' => [
                'access_token' => $res->access_token,
                'refresh_token' => $res->refresh_token ?? null
            ],
            'message' => 'Token refreshed successfully',
            'status' => 200
        ];
    }

    /**
     * Logout user and revoke tokens
     *
     * @param \App\Models\User $user
     * @return array Response array with status and message
     */
    public function logout(User $user): array
    {
        $token = $user->token();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Token not found',
                'status' => 400
            ];
        }

        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $token->id)
            ->update(['revoked' => true]);

        $token->revoke();

        return [
            'success' => true,
            'message' => 'Logged out successfully',
            'status' => 200
        ];
    }

    /**
     * Verify user's email with OTP
     *
     * @param string $email
     * @param string $verificationToken
     * @return array Response array with status and message
     */
    public function verifyOtp(string $email, string $verificationToken): array
    {
        $user = User::where('email', $email)
            ->where('verification_token', $verificationToken)
            ->where('token_expires', '>', now())
            ->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid or expired OTP',
                'status' => 404
            ];
        }

        $user->update([
            'verified' => User::VERIFIED_USER,
            'verification_token' => null,
            'token_expires' => null
        ]);

        return [
            'success' => true,
            'message' => 'Email verified successfully',
            'status' => 200
        ];
    }

    /**
     * Resend OTP to user's email
     *
     * @param string $email
     * @return array Response array with status and message
     */
    public function resendOtp(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404
            ];
        }

        $user->update([
            'verification_token' => User::generateVerificationCode(),
            'token_expires' => now()->addMinutes(10)
        ]);

        event(new UserRegistered($user));

        return [
            'success' => true,
            'message' => 'OTP sent successfully',
            'status' => 200
        ];
    }

    /**
     * Get user profile information
     *
     * @param \App\Models\User $user
     * @return array Response array with user data
     */
    public function getProfile(User $user): array
    {
        return [
            'success' => true,
            'data' => $user,
            'status' => 200
        ];
    }

    /**
     * Get appropriate error message for refresh token failures
     *
     * @param string $error
     * @return string
     */
    private function getRefreshTokenError(string $error): string
    {
        return match ($error) {
            'invalid_request' => 'Invalid refresh token request',
            'invalid_grant' => 'The refresh token is invalid or has been revoked',
            default => 'Failed to refresh token'
        };
    }
}
