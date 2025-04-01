<?php

namespace App\Services;

use App\Models\User;

class AuthService
{

    public function verifyOtp(Request $request)
    {
        $request->validate([
            // 'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
        ]);

        $user = User::where('verification_token', $request->otp)
            ->where('token_expires', '>', now())
            ->first();
        if (!$user) {
            return $this->errorResponse('Invalid or expired OTP', 400);
        }

        // Mark user as verified
        $user->verification_token = null;
        $user->token_expires = null;
        $user->verified = User::VERIFIED_USER;
        $user->save();

        return $this->showMessage('Email verified successfully', 200);
    }
}
