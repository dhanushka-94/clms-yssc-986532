<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Member;
use App\Models\Staff;
use App\Models\Player;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;

class FileUploadTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware, WithFaker;

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
    public function it_can_upload_member_files()
    {
        $profilePicture = UploadedFile::fake()->image('profile.jpg');
        $attachment1 = UploadedFile::fake()->create('document1.pdf', 100);
        $attachment2 = UploadedFile::fake()->image('image1.jpg');

        $response = $this->followingRedirects()
            ->post(route('members.store'), [
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'membership_type' => 'regular',
                'membership_fee' => 1000,
                'profile_picture' => $profilePicture,
                'attachments' => [$attachment1, $attachment2]
            ]);

        $response->assertOk();

        $member = Member::first();
        $this->assertNotNull($member);
        
        // Assert profile picture was stored
        if ($member->profile_picture) {
            Storage::disk('public')->assertExists($member->profile_picture);
        }
        
        // Assert attachments were stored
        if ($member->attachments) {
            foreach ($member->attachments as $attachment) {
                Storage::disk('public')->assertExists($attachment);
            }
        }
    }

    /** @test */
    public function it_can_upload_staff_files()
    {
        $profilePicture = UploadedFile::fake()->image('profile.jpg');
        $attachment1 = UploadedFile::fake()->create('document1.pdf', 100);
        $attachment2 = UploadedFile::fake()->image('image1.jpg');

        $response = $this->followingRedirects()
            ->post(route('staff.store'), [
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'role' => 'coach',
                'salary' => 50000,
                'profile_picture' => $profilePicture,
                'attachments' => [$attachment1, $attachment2]
            ]);

        $response->assertOk();

        $staff = Staff::first();
        $this->assertNotNull($staff);
        
        // Assert profile picture was stored
        if ($staff->profile_picture) {
            Storage::disk('public')->assertExists($staff->profile_picture);
        }
        
        // Assert attachments were stored
        if ($staff->attachments) {
            foreach ($staff->attachments as $attachment) {
                Storage::disk('public')->assertExists($attachment);
            }
        }
    }

    /** @test */
    public function it_can_upload_player_files()
    {
        $profilePicture = UploadedFile::fake()->image('profile.jpg');
        $attachment1 = UploadedFile::fake()->create('document1.pdf', 100);
        $attachment2 = UploadedFile::fake()->image('image1.jpg');

        $response = $this->followingRedirects()
            ->post(route('players.store'), [
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'jersey_number' => 10,
                'position' => 'striker',
                'contract_amount' => 100000,
                'profile_picture' => $profilePicture,
                'attachments' => [$attachment1, $attachment2]
            ]);

        $response->assertOk();

        $player = Player::first();
        $this->assertNotNull($player);
        
        // Assert profile picture was stored
        if ($player->profile_picture) {
            Storage::disk('public')->assertExists($player->profile_picture);
        }
        
        // Assert attachments were stored
        if ($player->attachments) {
            foreach ($player->attachments as $attachment) {
                Storage::disk('public')->assertExists($attachment);
            }
        }
    }

    /** @test */
    public function it_can_update_member_files()
    {
        $member = Member::factory()->create([
            'membership_type' => 'regular',
            'membership_fee' => 1000
        ]);
        
        $newProfilePicture = UploadedFile::fake()->image('new_profile.jpg');
        $newAttachment = UploadedFile::fake()->create('new_document.pdf', 100);

        $response = $this->followingRedirects()
            ->put(route('members.update', $member), [
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'membership_type' => 'regular',
                'membership_fee' => 1000,
                'profile_picture' => $newProfilePicture,
                'attachments' => [$newAttachment]
            ]);

        $response->assertOk();

        $member->refresh();
        
        // Assert new profile picture was stored
        if ($member->profile_picture) {
            Storage::disk('public')->assertExists($member->profile_picture);
        }
        
        // Assert new attachment was stored
        if ($member->attachments) {
            foreach ($member->attachments as $attachment) {
                Storage::disk('public')->assertExists($attachment);
            }
        }
    }

    /** @test */
    public function it_can_delete_member_files()
    {
        $profilePicture = UploadedFile::fake()->image('profile.jpg');
        $attachment = UploadedFile::fake()->create('document.pdf', 100);

        $member = Member::factory()->create([
            'membership_type' => 'regular',
            'membership_fee' => 1000,
            'profile_picture' => $profilePicture->store('profile-pictures/members', 'public'),
            'attachments' => [$attachment->store('attachments/members/1/documents', 'public')]
        ]);

        $oldProfilePicture = $member->profile_picture;
        $oldAttachments = $member->attachments;

        $response = $this->followingRedirects()
            ->delete(route('members.destroy', $member));

        $response->assertOk();

        // Assert files were deleted
        if ($oldProfilePicture) {
            Storage::disk('public')->assertMissing($oldProfilePicture);
        }
        
        if ($oldAttachments) {
            foreach ($oldAttachments as $attachment) {
                Storage::disk('public')->assertMissing($attachment);
            }
        }

        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }

    /** @test */
    public function it_validates_file_types_and_sizes()
    {
        $oversizedFile = UploadedFile::fake()->create('large.pdf', 3000); // 3MB
        $invalidType = UploadedFile::fake()->create('invalid.exe', 100);

        $response = $this->post(route('members.store'), [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'membership_type' => 'regular',
            'membership_fee' => 1000,
            'profile_picture' => $oversizedFile,
            'attachments' => [$invalidType]
        ]);

        $response->assertSessionHasErrors(['profile_picture', 'attachments.0']);
    }
} 