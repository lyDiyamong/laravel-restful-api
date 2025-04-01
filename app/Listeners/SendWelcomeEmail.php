<?php

namespace App\Listeners;

use App\Mail\WelcomeEmail;
use App\Events\UserRegistered;
use App\Jobs\SendWelcomeEmailJob;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
    
class SendWelcomeEmail implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        //
        SendWelcomeEmailJob::dispatch($event->user)->onQueue("auth");

    }
}
