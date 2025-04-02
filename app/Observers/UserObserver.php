<?php

namespace App\Observers;

use Exception;
use App\Models\User;
use App\Events\UserRegistered;
use Illuminate\Support\Facades\Log;
class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
        try {
            // Fire an event to let the listener send a welcome email
            Log::info("Firing event to send welcome email to {$user->email}");
            event(new UserRegistered($user));
        } catch (Exception $e) {
            Log::error("Failed to send mail to {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
