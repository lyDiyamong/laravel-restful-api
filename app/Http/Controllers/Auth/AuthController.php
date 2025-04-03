<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;

use Illuminate\Http\Request;
use App\Libraries\IssueToken;
use App\Services\AuthService;
use App\Events\UserRegistered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use Laravel\Passport\Client as OClient;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Cookie;

class AuthController extends ApiController
{
    protected AuthService $authService;

    private $client;

    /**
     * Create a new AuthController instance.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->client = OClient::where("password_client", 1)->first();
    }

    /**
     * Register a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $result = $this->authService->register($request->all());

        if (!$result['success']) {
            return $this->errorResponse(
                $result['errors'] ?? $result['message'],
                $result['status']
            );
        }

        return $this->successResponse([
            'message' => $result['message']
        ], $result['status']);
    }

    /**
     * Authenticate user and get token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $result = $this->authService->login($request->only('email', 'password'));

        if (!$result['success']) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        $response = $this->successResponse([
            'data' => ['access_token' => $result['data']['access_token']],
            'message' => $result['message']
        ]);

        if (isset($result['data']['refresh_token'])) {
            $response->withCookie($this->setRefreshCookie('refresh_token', $result['data']['refresh_token']));
        }

        return $response;
    }

    /**
     * Refresh access token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $result = $this->authService->refreshToken($request->cookie('refresh_token'));

        if (!$result['success']) {
            $response = $this->errorResponse($result['message'], $result['status']);

            if ($result['should_clear_cookie'] ?? false) {
                $response->withCookie(cookie()->forget('refresh_token'));
            }

            return $response;
        }

        $response = $this->successResponse([
            'data' => ['access_token' => $result['data']['access_token']],
            'message' => $result['message']
        ]);

        if (isset($result['data']['refresh_token'])) {
            $response->withCookie($this->setRefreshCookie('refresh_token', $result['data']['refresh_token']));
        }

        return $response;
    }

    /**
     * Logout user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $result = $this->authService->logout($request->user());

        if (!$result['success']) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse(['message' => $result['message']]);
    }

    /**
     * Get authenticated user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $result = $this->authService->getProfile($request->user());
        return $this->successResponse(['data' => $result['data']]);
    }

    /**
     * Verify user's email with OTP
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $result = $this->authService->verifyOtp(
            $request->email,
            $request->verification_token
        );

        if (!$result['success']) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse(['message' => $result['message']]);
    }

    /**
     * Resend OTP to user's email
     *
     * @param string $email
     * @return JsonResponse
     */
    public function resendOtp(string $email): JsonResponse
    {
        $result = $this->authService->resendOtp($email);

        if (!$result['success']) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse(['message' => $result['message']]);
    }

    /**
     * Create a refresh token cookie
     *
     * @param string $name
     * @param string $value
     * @return Cookie
     */
    protected function setRefreshCookie(string $name, string $value): Cookie
    {
        return cookie(
            $name,
            $value,
            60 * 24 * 30, // 30 days
            null,
            null,
            config('app.env') === 'production',
            true, // httpOnly
            false,
            'Strict'
        );
    }
}
