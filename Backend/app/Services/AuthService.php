<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Helpers\Error;

class AuthService
{
    /**
     * Attempt to authenticate the user.
     *
     * @param array<string, string> $data
     */
    public static function attempt($data): array|Error
    {
        // Retrieve user by credentials
        $user = User::where('email', $data['email'])->first();

        // Check if user exists
        if (!$user) {
            return new Error('Email atau password salah', 401);
        }

        // Attempt authentication
        $token = Auth::attempt($data);

        if (!$token) {
            return new Error('Email atau password salah', 401);
        }

        return static::withToken($token);
    }

    public static function register(array $data): array|Error
    {
        $user = User::create($data);

        return static::withToken(Auth::login($user));
    }

    public static function logout()
    {
        Auth::logout();

        return true;
    }

    public static function refresh()
    {
        return static::withToken(Auth::refresh());
    }

    public static function me()
    {
        return User::find(Auth::id());
    }

    public static function check()
    {
        if (!Auth::check()) {
            return new Error('You are not authorized to access this resource', 401);
        }

        return null;
    }

    private static function withToken($token)
    {
        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL(),
            'user' => static::me(),
        ];
    }
}
