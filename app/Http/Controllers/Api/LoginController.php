<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\TokenResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('guest:sanctum'),
        ];
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            "email" => ["required", "email"],
            "password" => ["required"],
            "device_name" => ["required"]
        ]);
        $user = User::whereEmail($request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                "email" => [__('auth.failed')]
            ]);
        }


        return new TokenResponse($user);
    }
}
