<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        $nameParts = explode(" ", $user->name);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? ''; // Will be empty string if not provided
    
        return [
            "user_identifier" => (string)$user->user_id,
            "first_name" => (string)$firstName,
            "last_name" => (string)$lastName,
            "user_email" => (string)$user->email,
            "isVerified" => (int)$user->verified,
            "creation_date" => $user->created_at,
        ];
    }
}
