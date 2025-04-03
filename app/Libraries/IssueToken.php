<?php

namespace App\Libraries;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Client as OClient;
use Laravel\Passport\Bridge\RefreshTokenRepository;

class IssueToken
{
    use ApiResponder;
    private $grantType = 'password';
    private $scope = '*';
    private $username = 'email';
    private $usernameValue = null;
    private static $instance = null;

    private function __construct() {}

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function scope(string $scope): self
    {
        $instance = self::instance();
        $instance->scope = $scope;
        return $instance;
    }

    public function setScope(string $scope): self
    {
        $this->scope = $scope;
        return $this;
    }

    public function setUsernameField(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function setUsernameValue(string $value): self
    {
        $this->usernameValue = $value;
        return $this;
    }

    public static function useRefreshTokenGrantType(): self
    {
        $instance = self::instance();
        $instance->grantType = 'refresh_token';
        return $instance;
    }

    public static function usePasswordGrantType(): self
    {
        $instance = self::instance();
        $instance->grantType = 'password';
        return $instance;
    }

    private function defaultParam(): array
    {
        $oClient = OClient::where('password_client', 1)->first();

        return [
            'grant_type' => $this->grantType,
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'scope' => $this->scope,
        ];
    }

    private function passwordParam(string $password): array
    {
        return [
            'password' => $password
        ];
    }

    public function issueToken(array $credentials = []): object
    {
        $req = request();
        $defaultParam = $this->defaultParam();

        // Only set username for password grant type
        $params = match ($this->grantType) {
            'password' => [
                ...$defaultParam,
                'username' => $this->usernameValue ?? $credentials[$this->username],
                'password' => $credentials['password']
            ],
            'refresh_token' => [
                ...$defaultParam,
                'refresh_token' => $req->refresh_token ?? $req->cookie('refresh_token')
            ],
            default => $defaultParam
        };

        $req->request->add($params);

        $tokenRequest = Request::create('/oauth/token', 'POST', $params);
        $res = Route::dispatch($tokenRequest);

        $statusCode = $res->getStatusCode();
        $responseJson = json_decode($res->getContent(), true);

        // Check if the response contains error information
        if (isset($responseJson['error'])) {
            return (object)[
                'res' => $res,
                'statusCode' => $statusCode,
                'error' => $responseJson['error'],
                'error_description' => $responseJson['error_description'] ?? 'Unknown error',
                'success' => false,
            ];
        }

        // Create result object with safe access to response keys
        return (object)[
            'res' => $res,
            'statusCode' => $statusCode,
            'access_token' => $responseJson['access_token'] ?? null,
            'refresh_token' => $responseJson['refresh_token'] ?? null,
            'success' => $statusCode == 200 && isset($responseJson['access_token']),
        ];
    }

    // Revoke token
    public function revokeToken(string $tokenId): void
    {
        $tokenRepository = app(TokenRepository::class);
        $refreshTokenRepository = app(RefreshTokenRepository::class);

        // Revoke access token
        $tokenRepository->revokeAccessToken($tokenId);

        // Revoke refresh token
        $refreshTokenRepository->revokeRefreshToken($tokenId);
    }
}
