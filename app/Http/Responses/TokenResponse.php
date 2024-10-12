<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class TokenResponse implements Responsable
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
    public function toResponse($request)
    {
        $token = $this->user->createToken(
            $request->device_name,
            $this->user->permissions->pluck('name')->toArray()
        );
        return response([
            "plain-text-token" => $token->plainTextToken
        ]);
    }
}
