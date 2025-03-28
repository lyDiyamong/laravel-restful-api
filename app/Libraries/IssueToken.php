<?php

namespace App\Libraries;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client as OClient;

class IssueToken
{
    use ApiResponder;
    private $grantType = 'password';
    private $scope = '*';
    private $username = 'email';
    private $usernameValue = null;
    private static $instance = null;

    private function __construct()
    {
    }

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

    public function issueToken($req): object
    {
        $defaultParam = $this->defaultParam();

        // Only set username for password grant type
        $params = match($this->grantType) {
           'password' => [
                ...$defaultParam,
                'username' => $this->usernameValue ?? $req->{$this->username},
                'password' => $req->password
            ],
            'refresh_token' => [
                ...$defaultParam,
                'refresh_token' => $req->refresh_token ?? $req->cookie('refresh_token')
            ],
            default => $defaultParam
        };

        
        $req->request->add($params);
        $req->headers->set('Accept', 'application/json');
        $req->headers->set('Content-Type', 'application/json');
        
        $tokenRequest = Request::create('/oauth/token', 'POST', $params);
        $res = Route::dispatch($tokenRequest);

        return (object)[
            'res' => $res,
            'statusCode' => $statusCode = $res->getStatusCode(),
            'json' => json_decode($res->getContent(), true),
            'success' => $statusCode == '200',
        ];
    }
}
