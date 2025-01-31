<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\AssertsCrudResponses;

abstract class BaseCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker, AssertsCrudResponses;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Create admin role
        $role = Role::create(['name' => 'admin']);

        // Create and authenticate user
        $this->user = User::factory()->create();
        $this->user->roles()->attach($role);
        $this->actingAs($this->user);

        // Disable CSRF verification for tests
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    protected function assertStoreOrUpdateResponse($response)
    {
        $response->assertStatus(302);
        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('success');
    }

    protected function assertValidationErrors($response, array $fields)
    {
        $response->assertStatus(302);
        $response->assertSessionHasErrors($fields);
    }

    protected function assertFileUploaded($path)
    {
        Storage::disk('public')->assertExists($path);
    }

    protected function assertFileDeleted($path)
    {
        Storage::disk('public')->assertMissing($path);
    }
} 