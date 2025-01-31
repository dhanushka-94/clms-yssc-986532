<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Player;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;

class PlayerCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
    }

    /** @test */
    public function it_can_list_players()
    {
        // Create some test players
        Player::factory()->count(5)->create();

        $response = $this->get(route('players.index'));

        $response->assertStatus(200);
        $response->assertViewIs('players.index');
        $response->assertViewHas('players');
        
        $players = $response->viewData('players');
        $this->assertEquals(5, $players->count());
    }

    /** @test */
    public function it_can_show_create_player_form()
    {
        $response = $this->get(route('players.create'));

        $response->assertStatus(200);
        $response->assertViewIs('players.create');
    }

    /** @test */
    public function it_can_create_player()
    {
        $profilePicture = UploadedFile::fake()->image('profile.jpg');
        $attachment = UploadedFile::fake()->create('document.pdf', 100);

        $playerData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'nic' => $this->faker->unique()->numerify('############'),
            'phone' => $this->faker->phoneNumber,
            'whatsapp_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'position' => 'striker',
            'date_of_birth' => $this->faker->date(),
            'joined_date' => $this->faker->date(),
            'contract_amount' => $this->faker->numberBetween(50000, 200000),
            'contract_start_date' => $this->faker->date(),
            'contract_end_date' => $this->faker->date(),
            'achievements' => $this->faker->text(),
            'status' => 'active',
            'profile_picture' => $profilePicture,
            'attachments' => [$attachment]
        ];

        $response = $this->post(route('players.store'), $playerData);

        $this->assertStoreOrUpdateResponse($response);
        
        // Assert the player was created in the database
        $this->assertDatabaseHas('players', [
            'first_name' => $playerData['first_name'],
            'last_name' => $playerData['last_name']
        ]);

        // Get the created player
        $player = Player::first();
        
        // Assert files were stored
        if ($player->profile_picture) {
            $this->assertFileUploaded($player->profile_picture);
        }
        
        if ($player->attachments) {
            foreach ($player->attachments as $attachment) {
                $this->assertFileUploaded($attachment);
            }
        }
    }

    /** @test */
    public function it_can_show_player_details()
    {
        $player = Player::factory()->create();

        $response = $this->get(route('players.show', $player));

        $response->assertStatus(200);
        $response->assertViewIs('players.show');
        $response->assertViewHas('player');
        
        $viewPlayer = $response->viewData('player');
        $this->assertEquals($player->id, $viewPlayer->id);
    }

    /** @test */
    public function it_can_show_edit_player_form()
    {
        $player = Player::factory()->create();

        $response = $this->get(route('players.edit', $player));

        $response->assertStatus(200);
        $response->assertViewIs('players.edit');
        $response->assertViewHas('player');
    }

    /** @test */
    public function it_can_update_player()
    {
        $player = Player::factory()->create();
        
        $newProfilePicture = UploadedFile::fake()->image('new_profile.jpg');
        $newAttachment = UploadedFile::fake()->create('new_document.pdf', 100);

        $updatedData = [
            'first_name' => 'Updated First Name',
            'last_name' => 'Updated Last Name',
            'role' => 'coach',
            'salary' => 50000,
            'profile_picture' => $newProfilePicture,
            'attachments' => [$newAttachment]
        ];

        $response = $this->put(route('players.update', $player), $updatedData);

        $this->assertStoreOrUpdateResponse($response);
        
        // Assert the player was updated in the database
        $this->assertDatabaseHas('players', [
            'id' => $player->id,
            'first_name' => $updatedData['first_name'],
            'last_name' => $updatedData['last_name']
        ]);

        // Refresh player from database
        $player->refresh();
        
        // Assert new files were stored
        if ($player->profile_picture) {
            $this->assertFileUploaded($player->profile_picture);
        }
        
        if ($player->attachments) {
            foreach ($player->attachments as $attachment) {
                $this->assertFileUploaded($attachment);
            }
        }
    }

    /** @test */
    public function it_can_delete_player()
    {
        $profilePicture = UploadedFile::fake()->image('profile.jpg');
        $attachment = UploadedFile::fake()->create('document.pdf', 100);

        $player = Player::factory()->create([
            'profile_picture' => $profilePicture->store('profile-pictures/players', 'public'),
            'attachments' => [$attachment->store('attachments/players/1/documents', 'public')]
        ]);

        $oldProfilePicture = $player->profile_picture;
        $oldAttachments = $player->attachments;

        $response = $this->delete(route('players.destroy', $player));

        $response->assertRedirect(route('players.index'));
        $response->assertSessionHas('success');
        
        // Assert player was deleted from database
        $this->assertDatabaseMissing('players', ['id' => $player->id]);
        
        // Assert files were deleted
        if ($oldProfilePicture) {
            $this->assertFileDeleted($oldProfilePicture);
        }
        
        if ($oldAttachments) {
            foreach ($oldAttachments as $attachment) {
                $this->assertFileDeleted($attachment);
            }
        }
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $response = $this->post(route('players.store'), []);

        $this->assertValidationErrors($response, ['first_name', 'last_name']);
    }

    /** @test */
    public function it_validates_file_uploads()
    {
        $oversizedFile = UploadedFile::fake()->create('large.pdf', 3000); // 3MB
        $invalidType = UploadedFile::fake()->create('invalid.exe', 100);

        $response = $this->post(route('players.store'), [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'position' => 'striker',
            'profile_picture' => $oversizedFile,
            'attachments' => [$invalidType]
        ]);

        $this->assertValidationErrors($response, ['profile_picture', 'attachments.0']);
    }

    /** @test */
    public function it_validates_unique_fields()
    {
        // Create a player
        $player = Player::factory()->create();

        // Try to create another player with the same NIC
        $response = $this->post(route('players.store'), [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'nic' => $player->nic,
            'position' => 'striker'
        ]);

        $this->assertValidationErrors($response, ['nic']);
    }

    /** @test */
    public function it_can_create_player_with_only_required_fields()
    {
        $playerData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
        ];

        $response = $this->post(route('players.store'), $playerData);

        $this->assertStoreOrUpdateResponse($response);
        
        // Assert the player was created in the database
        $this->assertDatabaseHas('players', [
            'first_name' => $playerData['first_name'],
            'last_name' => $playerData['last_name']
        ]);

        // Get the created player
        $player = Player::first();
        
        // Assert default values were set
        $this->assertEquals('active', $player->status);
        $this->assertEquals('unassigned', $player->position);
        $this->assertEquals(0, $player->contract_amount);
        $this->assertNotNull($player->joined_date);
        $this->assertNotNull($player->contract_start_date);
        $this->assertNotNull($player->contract_end_date);
    }
} 