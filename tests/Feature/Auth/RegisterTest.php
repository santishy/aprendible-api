<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();
        $this->withoutJsonApiHelpers();
    }
    public function test_can_register(): void
    {
        $this->withoutJsonApiDocumentFormatting();
        $response = $this->postJson(route('api.v1.register'), $data = $this->validateCredentials());

        $plainTextToken = $response->json('plain-text-token');

        $this->assertNotNull(PersonalAccessToken::findToken($plainTextToken));

        $this->assertDatabaseHas('users', [
            "name" => $data["name"],
            "email" => $data["email"]
        ]);
    }
    public function test_authenticated_users_cannot_register_again()
    {
        Sanctum::actingAs(User::factory()->create());
        $this->postJson(route('api.v1.register'))
            ->assertNoContent();
    }
    public function test_name_is_required()
    {
        $data = $this->validateCredentials(["name" => ""]);

        $this->postJson(route('api.v1.register'), $data)
            ->assertJsonValidationErrorFor('name');
    }
    public function test_email_is_required()
    {
        //dd($this->validateCredentials(["email" => ""]));
        $data = $this->validateCredentials(["email" => ""]);

        $this->postJson(route('api.v1.register'), $data)
            ->assertJsonValidationErrorFor('email');
    }
    public function test_email_must_be_valid()
    {
        //dd($this->validateCredentials(["email" => ""]));
        $data = $this->validateCredentials(["email" => "email-invalid"]);

        $this->postJson(route('api.v1.register'), $data)

            ->assertJsonValidationErrorFor('email');
    }
    public function test_email_must_be_unique()
    {
        $user = User::factory()->create();

        $data = $this->validateCredentials(["email" => $user->email]);

        $this->postJson(route('api.v1.register'), $data)
            ->assertJsonValidationErrorFor('email');
    }
    public function test_password_is_required()
    {
        $user = User::factory()->create();

        $data = $this->validateCredentials(["password" => ""]);

        $this->postJson(route('api.v1.register'), $data)
            ->assertJsonValidationErrorFor('password');
    }
    public function test_password_must_be_confirmed()
    {
        $data = $this->validateCredentials([
            "password" => "password",
            "password_confirmation" => "not-confirmed"
        ]);

        $this->postJson(route('api.v1.register'), $data)
            ->assertJsonValidationErrorFor('password');
    }
    public function test_device_name_is_required()
    {
        $data = $this->validateCredentials([
            "device_name" => "",
        ]);

        $this->postJson(route('api.v1.register'), $data)
            ->assertJsonValidationErrorFor('device_name');
    }
    public function validateCredentials(mixed $overrides = []): array
    {
        return array_merge([
            "name" => "santiago",
            "email" => "santi_shy@hotmail.com",
            "password" => "password",
            "password_confirmation" => "password",
            "device_name" => "My device"
        ], $overrides);
    }
}
