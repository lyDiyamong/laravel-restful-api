<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class UserCreated extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    public $otp;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        // $this->user = $user;
        // // Generate a 6-digit OTP (or use Str::uuid() for a longer token)
        // $this->otp = mt_rand(100000, 999999);
        
        // // Store the OTP and expiration in the user record
        // $user->verification_token = $this->otp;
        // $user->token_expires = now()->addMinutes(10); // Expires in 10 minutes
        // $user->save();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Our App - Verify Your Account',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'user' => $this->user,
                'otp' => $this->otp,
            ],
        );
    }

    // public function build() {
    //     return $this->subject("Your Otp code here")
    //     ->view("emails.welcome")
    //     ->with([
    //         'user' => $this->user,
    //         'otp' => $this->otp,
    //     ]);;
    // }
}