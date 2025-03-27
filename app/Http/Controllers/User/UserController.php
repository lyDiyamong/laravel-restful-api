<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends ApiController
{

    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::all();
        return $this->showAll($users, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

        return $this->showOne($user, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->showOne($user, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {

        $rule = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:8|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        ];

        $validator = Validator::make($request->all(), $rule);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
            
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email') && $user->email != $request->email) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->has('admin')) {
            if (!$user->isVerified()) {
                return $this->errorResponse('Only verified users can modify the admin field', 409);
            }

            $user->admin = $request->admin;
        }

        if (!$user->isDirty()) {
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        $user->save();

        return $this->showOne($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->showMessage("User has been deleted successfully", 200);
    }

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
            return $this->errorResponse( 'Invalid or expired OTP', 400);
        }
        
        // Mark user as verified
        $user->verification_token = null;
        $user->token_expires = null;
        $user->verified = User::VERIFIED_USER;
        $user->save();

        return $this->showMessage('Email verified successfully', 200);
    }

    public function resendOtp(string $email) {


        $user = User::where("email", $email)->first();

        if (!$user) {
            return $this->errorResponse("User not found", 404);
        }

        try {
            retry(5, function () use ($user)
            {
                Mail::to($user->email)->send(new UserCreated($user));
                Log::info("Mail resent successfully to: {$user->email}");
            }, 100);
        } catch (Exception $e) {
            Log::error("Failed to resend mail to {$user->email}: " . $e->getMessage());
        }



        return $this->showMessage("Please check your message again to claim a new verification code", 200);





    }
}
