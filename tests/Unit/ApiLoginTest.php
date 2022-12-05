<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Unit\factory;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Hash;

class ApiLoginTest extends TestCase
{

    public function testWeatherDataWithoutAuthentication()
    {

        $response = $this->get('api/home');

        $response->assertStatus(401);
    }

    public function testWeatherDataWithAuthentication()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('api/home');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user',
            'main',
        ]);
    }
}
