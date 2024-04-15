<?php

namespace App\Http\Requests\Api\Common;

use App\Models\User;

class AuthToken
{
    public static function getUserAccessToken(User $user): string
    {
        return $user
            ->createToken($user->name)
            ->plainTextToken
        ;
    }
}
