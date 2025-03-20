<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];
    protected array $availableIncludes = [];

    /**
     * Transform the user model data
     */
    public function transform(User $user): array
    {
        $nameParts = explode(" ", $user->name);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        return [
            "user_identifier" => (string) $user->user_id,
            "first_name" => $firstName,
            "last_name" => $lastName,
            "user_email" => (string) $user->email,
            "isVerified" => (int) $user->verified,
            "creation_date" => $user->created_at,
            "is_admin" => (string)$user->admin
        ];
    }

    /**
     * Maps transformed attribute names to original model fields
     * For sorting, filtering, etc.
     */
    public static function originalAttribute(string $index): ?string
    {
        $attributes = [
            "user_identifier" => "user_id",
            "first_name" => "name",      // we split 'name' for display
            "last_name" => "name",       // same
            "user_email" => "email",
            "isVerified" => "verified",
            "creation_date" => "created_at",
            "is_admin" => "admin"
        ];

        return $attributes[$index] ?? null;
    }
}
