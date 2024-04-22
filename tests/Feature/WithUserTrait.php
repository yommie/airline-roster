<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait WithUserTrait
{
    private const SAMPLE_REGISTRATION_DATA = [
        "name"      => "Yommie Airlines",
        "email"     => "yommie@airlines.com",
        "password"  => "password"
    ];

    private function makeUser(): User
    {
        $registrationData = self::SAMPLE_REGISTRATION_DATA;

        $registrationData["password"] = Hash::make($registrationData["password"]);

        $user = new User($registrationData);

        $user->save();

        return $user;
    }
}
