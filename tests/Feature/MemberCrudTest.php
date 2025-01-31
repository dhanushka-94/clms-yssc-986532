<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Member;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;

class MemberCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $member;

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

        // Create a test member
        $this->member = Member::factory()->create([
            'membership_type' => 'regular',
            'membership_fee' => 1000
        ]);

        // Disable CSRF verification for tests
        $this->withoutMiddleware();
    }

    /** @test */
    public function it_can_list_members()
    {
        // Create some additional test members
        Member::factory()->count(4)->create();

        $response = $this->get(route('members.index'));

        $response->assertStatus(200);
        $response->assertViewIs('members.index');
        $response->assertViewHas('members');
        
        $members = $response->viewData('members');
        $this->assertEquals(5, $members->count());
    }

    /** @test */
    public function it_can_show_create_member_form()
    {
        $response = $this->get(route('members.create'));

        $response->assertStatus(200);
        $response->assertViewIs('members.create');
    }

    /** @test */
    public function it_can_create_member()
    {
        $profilePicture = UploadedFile::fake()->image('profile.jpg');
        $attachment = UploadedFile::fake()->create('document.pdf', 100);

        $memberData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'nic' => $this->faker->unique()->numerify('############'),
            'phone' => $this->faker->phoneNumber,
            'whatsapp_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'date_of_birth' => $this->faker->date(),
            'joined_date' => $this->faker->date(),
            'membership_type' => 'regular',
            'designation' => $this->faker->jobTitle,
            'membership_fee' => $this->faker->randomFloat(2, 1000, 10000),
            'status' => 'active',
            'profile_picture' => $profilePicture,
            'attachments' => [$attachment]
        ];

        $response = $this->post(route('members.store'), $memberData);

        $response->assertStatus(302);
        $response->assertRedirect(route('members.index'));
        
        // Assert the member was created in the database
        $this->assertDatabaseHas('members', [
            'first_name' => $memberData['first_name'],
            'last_name' => $memberData['last_name'],
            'membership_type' => $memberData['membership_type']
        ]);

        // Get the created member
        $member = Member::where('first_name', $memberData['first_name'])->first();
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
    public function it_can_show_member_details()
    {
        $response = $this->get(route('members.show', $this->member));

        $response->assertStatus(200);
        $response->assertViewIs('members.show');
        $response->assertViewHas('member');
        
        $viewMember = $response->viewData('member');
        $this->assertEquals($this->member->id, $viewMember->id);
    }

    /** @test */
    public function it_can_show_edit_member_form()
    {
        $response = $this->get(route('members.edit', $this->member));

        $response->assertStatus(200);
        $response->assertViewIs('members.edit');
        $response->assertViewHas('member');
    }

    /** @test */
    public function it_can_update_member()
    {
        $newProfilePicture = UploadedFile::fake()->image('new_profile.jpg');
        $newAttachment = UploadedFile::fake()->create('new_document.pdf', 100);

        $updatedData = [
            'first_name' => 'Updated First Name',
            'last_name' => 'Updated Last Name',
            'membership_type' => 'regular',
            'membership_fee' => 2000,
            'profile_picture' => $newProfilePicture,
            'attachments' => [$newAttachment]
        ];

        $response = $this->put(route('members.update', $this->member), $updatedData);

        $response->assertStatus(302);
        $response->assertRedirect(route('members.index'));
        
        // Assert the member was updated in the database
        $this->assertDatabaseHas('members', [
            'id' => $this->member->id,
            'first_name' => $updatedData['first_name'],
            'last_name' => $updatedData['last_name']
        ]);

        // Refresh member from database
        $this->member->refresh();
        
        // Assert new profile picture was stored
        if ($this->member->profile_picture) {
            Storage::disk('public')->assertExists($this->member->profile_picture);
        }
        
        // Assert new attachment was stored
        if ($this->member->attachments) {
            foreach ($this->member->attachments as $attachment) {
                Storage::disk('public')->assertExists($attachment);
            }
        }
    }

    /** @test */
    public function it_can_delete_member()
    {
        $profilePicture = UploadedFile::fake()->image('profile.jpg');
        $attachment = UploadedFile::fake()->create('document.pdf', 100);

        $member = Member::factory()->create([
            'membership_type' => 'regular',
            'membership_fee' => 1000,
            'profile_picture' => $profilePicture->store('profile-pictures/members', 'public'),
            'attachments' => json_encode([$attachment->store('attachments/members/1/documents', 'public')])
        ]);

        $oldProfilePicture = $member->profile_picture;
        $oldAttachments = json_decode($member->attachments, true);

        $response = $this->delete(route('members.destroy', $member));

        $response->assertStatus(302);
        $response->assertRedirect(route('members.index'));
        
        // Assert member was deleted from database
        $this->assertDatabaseMissing('members', ['id' => $member->id]);
        
        // Assert files were deleted
        if ($oldProfilePicture) {
            Storage::disk('public')->assertMissing($oldProfilePicture);
        }
        
        if ($oldAttachments) {
            foreach ($oldAttachments as $attachment) {
                Storage::disk('public')->assertMissing($attachment);
            }
        }
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $response = $this->post(route('members.store'), []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['first_name', 'last_name']);
    }

    /** @test */
    public function it_validates_file_uploads()
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

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['profile_picture', 'attachments.0']);
    }

    /** @test */
    public function it_validates_unique_fields()
    {
        // Create a member
        $member = Member::factory()->create();

        // Try to create another member with the same NIC
        $response = $this->post(route('members.store'), [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'nic' => $member->nic,
            'membership_type' => 'regular',
            'membership_fee' => 1000
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['nic']);
    }
} 