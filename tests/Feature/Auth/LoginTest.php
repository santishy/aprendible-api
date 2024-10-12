<?php

namespace Tests\Feature\Auth;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LoginTest extends TestCase implements HasMiddleware
{
    use RefreshDatabase;

    public static function middleware()
    {
        return [
            new Middleware('guest:sanctum')
        ];
    }
    public function test_can_issue_access_token(): void
    {
        //si falla alguna ruta en el futoro en postman imprimir route('login')
        $user = User::factory()->create();
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validateCredentials(["email" => $user->email]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $plainTextToken = $response->json('plain-text-token');

        $token = PersonalAccessToken::findToken($plainTextToken);

        $this->assertTrue($token->tokenable->is($user));
    }
    public function test_only_one_access_token_can_issued_at_a_time(): void
    {
        $this->withoutJsonApiDocumentFormatting();
        $user = User::factory()->create();
        //esto es asi, por sanctum:actionAs es un mockering y no crea el token por eso lo hacemos manual
        $plainTextToken = $user->createToken($user->name)->plainTextToken;
        //se manda en el header la autenticacion por que actingAs lo hacia internamente para poder loguear
        $this->withHeader('Authorization', 'Bearer ' . $plainTextToken);


        $response = $this->postJson(route('api.v1.login'))->dump()->assertNoContent();

        //$this->assertCount(1, PersonalAccessToken::findToken($plainTextToken)->first());
    }

    public function test_user_permissions_are_assigned_as_abilities_to_the_token()
    {
        $user = User::factory()->create();

        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();
        $permission3 = Permission::factory()->create();

        $user->givePermissionTo($permission1);
        $user->givePermissionTo($permission2);

        $data = $this->validateCredentials(["email" => $user->email]);

        $this->withoutJsonApiDocumentFormatting();

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
            "email" => "santi_shy@hotmail.com",
            "password" => "password",
            "device_name" => "My device"
        ], $overrides);
    }

    public function test_password_must_be_valid()
    {
        $user = User::factory()->create();
        $this->withoutJsonApiDocumentFormatting();
        $data = $this->validateCredentials(["email" => $user->email, "password" => "incorrect"]);
        $response = $this->postJson(route('api.v1.login'), $data)->dump();
        $response->assertJsonValidationErrorFor("email");
    }

    public function test_user_must_be_registered()
    {

        $this->withoutJsonApiDocumentFormatting();

        $response = $this->postJson(
            route('api.v1.login'),
            $this->validateCredentials()
        );

        $response->assertJsonValidationErrorFor('email');
    }

    public function test_email_must_be_required()
    {
        $this->withoutJsonApiDocumentFormatting();

        $response = $this->postJson(
            route('api.v1.login'),
            $this->validateCredentials(["email" => null])
        );

        $response->assertJsonValidationErrors(["email" => "required"]);
    }

    public function test_email_must_be_valid()
    {
        $this->withoutJsonApiDocumentFormatting();

        $response = $this->postJson(
            route('api.v1.login'),
            $this->validateCredentials(["email" => "email-invalid"])
        );

        $response->assertJsonValidationErrors(["email" => "email"]);
    }
    public function test_password_must_be_required()
    {
        $this->withoutJsonApiDocumentFormatting();

        $response = $this->postJson(
            route('api.v1.login'),
            $this->validateCredentials(["password" => null])
        );

        $response->assertJsonValidationErrors(["password" => "required"]);
    }
    public function test_device_name_must_be_required()
    {
        $this->withoutJsonApiDocumentFormatting();

        $response = $this->postJson(
            route('api.v1.login'),
            $this->validateCredentials(["device_name" => null])
        );

        $response->assertJsonValidationErrors(["device_name" => "required"]);
    }
}
