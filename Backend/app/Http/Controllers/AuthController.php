<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $payload = AuthService::attempt($credentials);

        return $this->respond($payload);
    }

    public function logout()
    {
        AuthService::logout();

        return $this->respond([
            'status' => 'success',
            'message' => 'Berhasil logout',
        ]);
    }

    public function refresh()
    {
        $payload = AuthService::refresh();

        return $this->respond($payload);
    }

    public function me()
    {
        return $this->respond(AuthService::me());
    }
}
