<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AccessTokenTest extends TestCase
{
    use RefreshDatabase;

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
