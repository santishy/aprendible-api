<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function SetUp(): void
    {
        parent::setUp();
        $this->withoutJsonApiHelpers();
    }

    public function test_can_issue_access_token(): void
    {
        //si falla alguna ruta en el futoro en postman imprimir route('login')
        $user = User::factory()->create();

        $data = $this->validateCredentials(['email' => $user->email]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $plainTextToken = $response->json('plain-text-token');

        $token = PersonalAccessToken::findToken($plainTextToken);

        $this->assertTrue($token->tokenable->is($user));
    }

    public function test_only_one_access_token_can_issued_at_a_time(): void
    {
        $user = User::factory()->create();
        //esto es asi, por sanctum:actionAs es un mockering y no crea el token por eso lo hacemos manual
        $plainTextToken = $user->createToken($user->name)->plainTextToken;
        //se manda en el header la autenticacion por que actingAs lo hacia internamente para poder loguear
        $this->withHeader('Authorization', 'Bearer '.$plainTextToken);

        $response = $this->postJson(route('api.v1.login'))->assertNoContent();

        $this->assertCount(1, $user->tokens);
    }

    public function test_user_permissions_are_assigned_as_abilities_to_the_token()
    {
        $user = User::factory()->create();

        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();
        $permission3 = Permission::factory()->create();

        $user->givePermissionTo($permission1);
        $user->givePermissionTo($permission2);

        $data = $this->validateCredentials(['email' => $user->email]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $plainTextToken = $response->json('plain-text-token');

        $dbToken = PersonalAccessToken::findToken($plainTextToken);

        $this->assertTrue($dbToken->can($permission1->name));
        $this->assertTrue($dbToken->can($permission2->name));

        $this->assertFalse($dbToken->can($permission3->name));
    }

    public function validateCredentials(mixed $overrides = []): array
    {
        return array_merge([
            'email' => 'santi_shy@hotmail.com',
            'password' => 'password',
            'device_name' => 'My device',
        ], $overrides);
    }

    public function test_password_must_be_valid()
    {
        $user = User::factory()->create();
        $data = $this->validateCredentials(['email' => $user->email, 'password' => 'incorrect']);
        $response = $this->postJson(route('api.v1.login'), $data);
        $response->assertJsonValidationErrorFor('email');
    }

    public function test_user_must_be_registered()
    {

        $response = $this->postJson(
            route('api.v1.login'),
            $this->validateCredentials()
        );

        $response->assertJsonValidationErrorFor('email');
    }

    public function test_email_must_be_required()
    {

        $response = $this->postJson(
            route('api.v1.login'),
            $this->validateCredentials(['email' => null])
        );

        $response->assertJsonValidationErrors(['email' => 'required']);
    }

    public function test_email_must_be_valid()
    {

        $response = $this->postJson(
            route('api.v1.login'),
            $this->validateCredentials(['email' => 'email-invalid'])
        );

        $response->assertJsonValidationErrors(['email' => 'email']);
    }

    public function test_password_must_be_required()
    {

        $response = $this->postJson(
            route('api.v1.login'),
            $this->validateCredentials(['password' => null])
        );

        $response->assertJsonValidationErrors(['password' => 'required']);
    }

    public function test_device_name_must_be_required()
    {

        $response = $this->postJson(
            route('api.v1.login'),
            $this->validateCredentials(['device_name' => null])
        );

        $response->assertJsonValidationErrors(['device_name' => 'required']);
    }
}
