<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public static function jwt($user)
    {
        $payload = [
            'iss' => 'lumen-jwt',
            'sub' => $user->id,
            'name' => $user->username,
            'email' => $user->email,
            'is_admin' => $user->is_admin,
            'iat' => time(),
            'exp' => time() + 60 * 60,
        ];

        return JWT::encode($payload, env('JWT_SECRET'));
    }
}
