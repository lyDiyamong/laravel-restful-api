<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Libraries\IssueToken;
use App\Http\Controllers\ApiController;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends ApiController
{
    //
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();


            $user = User::updateOrCreate([
                'email' => $socialUser->getEmail(),
            ], [
                'name' => $socialUser->getName(),
                'password' => bcrypt($socialUser->getId()),
                "img_profile" => $socialUser->getAvatar(),
                "provider" => $provider,
                "verified"=> true
            ]);

            // Generate a token for the user
            $token = IssueToken::usePasswordGrantType()
                ->issueToken([
                    "email" => $user->email,
                    "password" => $socialUser->getId()
                ]);
            // dd($token);

            

            if (!$token || isset($token['refresh_token'])) {
                return $this->errorResponse('No token', 401);
            }

            $response = $this->successResponse([
                'data' => [
                    'access_token' => $token->access_token,
                ],
                "message" => "Login successfully"
            ]);

            if (isset($token->refresh_token)) {
                $response->withCookie($this->setRefreshCookie('refresh_token', $token->refresh_token));
            }

            return $response;
        } catch (\Exception $e) {
            $this->errorResponse($e->getMessage(), 400);
        }
    }
}
