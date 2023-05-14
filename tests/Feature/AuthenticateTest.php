<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

uses(WithFaker::class, RefreshDatabase::class);

it('Email and Password required for login', function () {
    $response = $this->postJson('/api/v1/login', []);
    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => ['Email field is required'],
                'password' => ['Password field is required'],
            ],
        ]);
});

it('User login successfully', function () {
    $this->artisan('db:seed');
    $user = ['email' => 'user@gmail.com', 'password' => 'User@123'];
    $response = $this->postJson('/api/v1/login', $user);
    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'message',
            'data' => [
                'access_token',
                'token_type',
            ],
        ]);
});

it('User logout successfully', function () {
    $this->artisan('db:seed');
    $user = ['email' => 'user@gmail.com', 'password' => 'User@123'];
    Auth::attempt($user);
    $token = Auth::user()->createToken('access_token')->plainTextToken;
    $headers = ['Authorization' => "Bearer $token"];
    $response = $this->postJson('/api/v1/logout', [], $headers);
    $response->assertStatus(Response::HTTP_OK);
});

it('User Register successfully', function () {
    $register = [
        'name' => $this->faker->name,
        'email' => $this->faker->email,
        'password' => 'testpass',
        'password_confirmation' => 'testpass',
    ];
    $response = $this->postJson('/api/v1/register', $register);
    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'message',
            'data' => [
                'access_token',
                'token_type',
            ],
        ]);
});

it('User Register required name and password', function () {

    $response = $this->postJson('/api/v1/register', []);
    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'name' => ['Name field is required'],
                'email' => ['Email field is required'],
                'password' => ['Password field is required'],
            ],
        ]);
});

it('User Register required password confirmation', function () {

    $register = [
        'name' => $this->faker->name,
        'email' => $this->faker->email,
        'password' => $this->faker->password,
    ];
    $response = $this->postJson('/api/v1/register', $register);
    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'password' => ['The password field confirmation does not match.'],
            ],
        ]);
});
