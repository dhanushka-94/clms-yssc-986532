<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Staff;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;

class StaffCrudTest extends TestCase
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
    public function it_can_list_staff()
    {
        // Create some test staff members
        Staff::factory()->count(5)->create();

        $response = $this->get(route('staff.index'));

        $response->assertStatus(200);
        $response->assertViewIs('staff.index');
        $response->assertViewHas('staff');
        
        $staffMembers = $response->viewData('staff');
        $this->assertEquals(5, $staffMembers->count());
    }

    /** @test */
    public function it_can_show_create_staff_form()
    {
        $response = $this->get(route('staff.create'));

        $response->assertStatus(200);
        $response->assertViewIs('staff.create');
    }

    /** @test */
    public function it_can_create_staff()
    {
        $profilePicture = UploadedFile::fake()->image('profile.jpg');
        $attachment = UploadedFile::fake()->create('document.pdf', 100);

        $staffData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'nic' => $this->faker->unique()->numerify('############'),
            'phone' => $this->faker->phoneNumber,
            'whatsapp_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'role' => 'coach',
            'date_of_birth' => $this->faker->date(),
            'joined_date' => $this->faker->date(),
            'contract_start_date' => $this->faker->date(),
            'contract_end_date' => $this->faker->date(),
            'salary' => $this->faker->numberBetween(30000, 100000),
            'status' => 'active',
            'profile_picture' => $profilePicture,
            'attachments' => [$attachment]
        ];

        $response = $this->post(route('staff.store'), $staffData);

        $this->assertStoreOrUpdateResponse($response);
        
        // Assert the staff member was created in the database
        $this->assertDatabaseHas('staff', [
            'first_name' => $staffData['first_name'],
            'last_name' => $staffData['last_name'],
            'role' => $staffData['role']
        ]);

        // Get the created staff member
        $staff = Staff::first();
        
        // Assert files were stored
        if ($staff->profile_picture) {
            $this->assertFileUploaded($staff->profile_picture);
        }
        
        if ($staff->attachments) {
            foreach ($staff->attachments as $attachment) {
                $this->assertFileUploaded($attachment);
            }
        }
    }

    /** @test */
    public function it_can_show_staff_details()
    {
        $staff = Staff::factory()->create();

        $response = $this->get(route('staff.show', $staff));

        $response->assertStatus(200);
        $response->assertViewIs('staff.show');
        $response->assertViewHas('staff');
        
        $viewStaff = $response->viewData('staff');
        $this->assertEquals($staff->id, $viewStaff->id);
    }

    /** @test */
    public function it_can_show_edit_staff_form()
    {
        $staff = Staff::factory()->create();

        $response = $this->get(route('staff.edit', $staff));

        $response->assertStatus(200);
        $response->assertViewIs('staff.edit');
        $response->assertViewHas('staff');
    }

    /** @test */
    public function it_can_update_staff()
    {
        $staff = Staff::factory()->create();
        
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

        $response = $this->put(route('staff.update', $staff), $updatedData);

        $this->assertStoreOrUpdateResponse($response);
        
        // Assert the staff member was updated in the database
        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'first_name' => $updatedData['first_name'],
            'last_name' => $updatedData['last_name']
        ]);

        // Refresh staff from database
        $staff->refresh();
        
        // Assert new files were stored
        if ($staff->profile_picture) {
            $this->assertFileUploaded($staff->profile_picture);
        }
        
        if ($staff->attachments) {
            foreach ($staff->attachments as $attachment) {
                $this->assertFileUploaded($attachment);
            }
        }
    }

    /** @test */
    public function it_can_delete_staff()
    {
        $profilePicture = UploadedFile::fake()->image('profile.jpg');
        $attachment = UploadedFile::fake()->create('document.pdf', 100);

        $staff = Staff::factory()->create([
            'profile_picture' => $profilePicture->store('profile-pictures/staff', 'public'),
            'attachments' => [$attachment->store('attachments/staff/1/documents', 'public')]
        ]);

        $oldProfilePicture = $staff->profile_picture;
        $oldAttachments = $staff->attachments;

        $response = $this->delete(route('staff.destroy', $staff));

        $response->assertRedirect(route('staff.index'));
        $response->assertSessionHas('success');
        
        // Assert staff member was deleted from database
        $this->assertDatabaseMissing('staff', ['id' => $staff->id]);
        
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
        $response = $this->post(route('staff.store'), []);

        $this->assertValidationErrors($response, ['first_name', 'last_name']);
    }

    /** @test */
    public function it_validates_file_uploads()
    {
        $oversizedFile = UploadedFile::fake()->create('large.pdf', 3000); // 3MB
        $invalidType = UploadedFile::fake()->create('invalid.exe', 100);

        $response = $this->post(route('staff.store'), [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'role' => 'coach',
            'salary' => 50000,
            'profile_picture' => $oversizedFile,
            'attachments' => [$invalidType]
        ]);

        $this->assertValidationErrors($response, ['profile_picture', 'attachments.0']);
    }

    /** @test */
    public function it_validates_unique_fields()
    {
        // Create a staff member
        $staff = Staff::factory()->create();

        // Try to create another staff member with the same NIC
        $response = $this->post(route('staff.store'), [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'nic' => $staff->nic,
            'role' => 'coach',
            'salary' => 50000
        ]);

        $this->assertValidationErrors($response, ['nic']);
    }
} 