<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PasswordReset;
use Tests\TestCase;
use Illuminate\Support\Str;

class AuthTest extends TestCase
{    
    protected $token;

    /** @test */
    public function test_required_fields_for_registration()
    {
        $response = $this->post('/api/register');

        $response->assertSessionHasErrors('name');
        $response->assertSessionHasErrors('email');
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function test_name_is_not_profanity_in_registration()
    {
        $userData = [
            "name" => "bitch",
            "email" => Str::random(6)."@gmail.com",
            "password" => "888ceyhun"
        ];

        $response = $this->post('/api/register', $userData);
        $response->assertSessionHasErrors('name');
    }
    
    /** @test */
    public function test_successful_registration()
    {
        $this->withoutExceptionHandling();

        $userData = [
            "name" => "Jeyhun",
            "email" => Str::random(6)."@gmail.com",
            "password" => "888ceyhun"
        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertOk();
    }

    /** @test */
    public function test_required_fields_for_login()
    {
        $response = $this->post('/api/login');

        $response->assertSessionHasErrors('email');
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function test_successful_login()
    {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create(['password' => bcrypt('sample123')]);
    
        $response = $this->post('/api/login',[
            'email' => $user->email,
            'password' => 'sample123',
        ])->assertOk();

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_can_send_resend_email()
    {
        $this->withoutExceptionHandling();
        
        $user = User::latest()->first();
        $token = $user->createToken('Abyss Personal Access Client')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->json('POST', '/api/email/resend');

        $response->assertOk();
    }
    
    /** @test */
    public function user_can_send_password_reset_mail()
    {
        $this->withoutExceptionHandling();
        
        $user = User::latest()->first();
        
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST', '/api/password/create',[
            'email' => $user->email
        ]);

        $response->assertOk();
    }

    /** @test */
    public function user_can_change_password()
    {
        $this->withoutExceptionHandling();
        
        $user = User::latest()->first();
        $password_reset = PasswordReset::where('email', $user->email)->first();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST', '/api/password/reset',[
            'email' => $user->email,
            'password' => '888ceyhun',
            'token' => $password_reset->token
        ]);

        $response->assertOk();
    }
}
