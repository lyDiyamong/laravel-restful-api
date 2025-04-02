<?php

namespace App\Jobs;

use Throwable;
use App\Models\User;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailJob extends Job
{

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        \Log::info("(Job) , Sending welcome email to user {$this->user->id}, {$this->user->email}");
        Mail::to($this->user->email)->send(new WelcomeEmail($this->user));
    }

    public function failed(Throwable $exception): void
    {
        \Log::error("Failed to send welcome email to user {$this->user->id}", [
            'error' => $exception->getMessage()
        ]);
    }
}
