<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();
        $this->withoutJsonApiHelpers();
    }

    public function test_can_logout(): void
    {
        $user = User::factory()->create();
        //esto es asi, por sanctum:actionAs es un mockering y no crea el token por eso lo hacemos manual
        $plainTextToken = $user->createToken($user->name)->plainTextToken;
        //se manda en el header la autenticacion por que actingAs lo hacia internamente para poder loguear
        $this->withHeader('Authorization', 'Bearer ' . $plainTextToken);


        $response = $this->postJson(route('api.v1.logout'))->assertNoContent();

        $this->assertNull(PersonalAccessToken::findToken($plainTextToken));
    }
}
