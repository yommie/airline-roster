<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\Common\AuthToken;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email'     => ['required', 'string', 'email'],
            'password'  => ['required', 'string', 'min:8'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): string
    {
        $user = User::where("email", $this["email"])->first();

        if (!$user || !Hash::check($this["password"], $user->password)) {
            throw ValidationException::withMessages([
                "email" => trans("auth.failed"),
            ]);
        }

        return AuthToken::getUserAccessToken($user);
    }
}
