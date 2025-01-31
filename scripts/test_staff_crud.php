<?php

require __DIR__.'/../vendor/autoload.php';

// Load environment variables
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Staff;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

echo "Starting Staff CRUD Test Script...\n\n";

try {
    // Test Create
    echo "Testing Staff Creation...\n";
    $staff = new Staff([
        'first_name' => 'John',
        'last_name' => 'Coach',
        'nic' => '123456789012',
        'phone' => '1234567890',
        'whatsapp_number' => '1234567890',
        'address' => '123 Test Street',
        'role' => 'coach',
        'date_of_birth' => '1985-01-01',
        'joined_date' => '2024-01-01',
        'contract_start_date' => '2024-01-01',
        'contract_end_date' => '2025-01-01',
        'salary' => 50000,
        'status' => 'active'
    ]);

    // Create a test profile picture
    $profilePicture = UploadedFile::fake()->image('profile.jpg');
    $profilePath = $profilePicture->store('profile-pictures/staff', 'public');
    $staff->profile_picture = $profilePath;

    // Create test attachments
    $attachment1 = UploadedFile::fake()->create('contract.pdf', 100);
    $attachment2 = UploadedFile::fake()->create('certificate.pdf', 100);
    $attachmentPaths = [
        $attachment1->store('attachments/staff/documents', 'public'),
        $attachment2->store('attachments/staff/documents', 'public')
    ];
    $staff->attachments = json_encode($attachmentPaths);

    $staff->save();
    echo "✓ Staff member created successfully with ID: {$staff->id}\n";

    // Test Read
    echo "\nTesting Staff Retrieval...\n";
    $foundStaff = Staff::find($staff->id);
    if ($foundStaff) {
        echo "✓ Staff member found:\n";
        echo "  Name: {$foundStaff->first_name} {$foundStaff->last_name}\n";
        echo "  Role: {$foundStaff->role}\n";
        echo "  Salary: {$foundStaff->salary}\n";
    } else {
        throw new Exception("Failed to find staff member");
    }

    // Test Update
    echo "\nTesting Staff Update...\n";
    $foundStaff->first_name = 'Jane';
    $foundStaff->last_name = 'Manager';
    $foundStaff->role = 'manager';
    $foundStaff->salary = 60000;
    
    // Update profile picture
    $newProfilePicture = UploadedFile::fake()->image('new_profile.jpg');
    if ($foundStaff->profile_picture) {
        Storage::disk('public')->delete($foundStaff->profile_picture);
    }
    $newProfilePath = $newProfilePicture->store('profile-pictures/staff', 'public');
    $foundStaff->profile_picture = $newProfilePath;

    // Update attachments
    $newAttachment = UploadedFile::fake()->create('new_contract.pdf', 100);
    $oldAttachments = json_decode($foundStaff->attachments, true) ?? [];
    foreach ($oldAttachments as $path) {
        Storage::disk('public')->delete($path);
    }
    $newAttachmentPath = $newAttachment->store('attachments/staff/documents', 'public');
    $foundStaff->attachments = json_encode([$newAttachmentPath]);

    $foundStaff->save();
    echo "✓ Staff member updated successfully\n";

    // Test Delete
    echo "\nTesting Staff Deletion...\n";
    // Delete files first
    if ($foundStaff->profile_picture) {
        Storage::disk('public')->delete($foundStaff->profile_picture);
    }
    $attachments = json_decode($foundStaff->attachments, true) ?? [];
    foreach ($attachments as $path) {
        Storage::disk('public')->delete($path);
    }
    
    $foundStaff->delete();
    echo "✓ Staff member deleted successfully\n";

    // Verify deletion
    $deletedStaff = Staff::find($staff->id);
    if (!$deletedStaff) {
        echo "✓ Verified staff member no longer exists in database\n";
    } else {
        throw new Exception("Staff member still exists after deletion");
    }

    echo "\nAll tests completed successfully! ✨\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 