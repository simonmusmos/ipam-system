<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\IpAddress;
use App\Services\IpAddressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class IpAddressControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $mockService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \App\Http\Middleware\JwtMiddleware::class,
        ]);

        // Mock the service
        $this->mockService = $this->mock(IpAddressService::class);
    }

    public function test_index_returns_paginated_results()
    {
        $this->mockService
            ->shouldReceive('get')
            ->once()
            ->andReturn(collect(['mocked data']));

        $response = $this->getJson('/api/ip-addresses');

        $response->assertStatus(200);
    }

    public function test_get_details_returns_single_record()
    {
        $ip = IpAddress::factory()->create();

        $this->mockService
            ->shouldReceive('getDetails')
            ->once()
            ->withArgs(fn($arg) => $arg->id === $ip->id)
            ->andReturn($ip);

        $response = $this->getJson("/api/ip-addresses/{$ip->id}");

        $response->assertStatus(200)
                ->assertJsonFragment(['id' => $ip->id]);
    }

    public function test_store_creates_new_ip_address()
    {
        $payload = [
            'address' => '192.168.1.100',
            'label' => 'Test Label',
            'comment' => 'Optional comment',
        ];

        $this->mockService
            ->shouldReceive('create')
            ->once()
            ->andReturn(new IpAddress($payload));

        $response = $this->postJson('/api/ip-addresses', $payload + ['user_id' => 1]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['label' => 'Test Label']);
    }

    public function test_update_modifies_existing_ip_address()
    {
        $ip = IpAddress::factory()->create();

        $payload = ['label' => 'Updated Label', 'comment' => 'Updated comment'];

        $this->mockService
            ->shouldReceive('update')
            ->once()
            ->andReturn(new IpAddress(array_merge($ip->toArray(), $payload)));

        $response = $this->putJson("/api/ip-addresses/{$ip->id}", $payload + ['user_id' => 1, 'role' => 'super-admin']);

        $response->assertStatus(200)
                 ->assertJsonFragment(['label' => 'Updated Label']);
    }

    public function test_destroy_deletes_ip_address()
    {
        $ip = IpAddress::factory()->create();

        $this->mockService
            ->shouldReceive('delete')
            ->once()
            ->andReturnNull();

        $response = $this->deleteJson("/api/ip-addresses/{$ip->id}", ['user_id' => 1, 'role' => 'super-admin']);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Deleted']);
    }
}
