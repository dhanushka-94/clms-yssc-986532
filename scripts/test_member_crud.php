<?php

require __DIR__.'/../vendor/autoload.php';

// Load environment variables
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

echo "Starting Member CRUD Test Script...\n\n";

try {
    // Test Create
    echo "Testing Member Creation...\n";
    $member = new Member([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'nic' => '123456789012',
        'phone' => '1234567890',
        'whatsapp_number' => '1234567890',
        'address' => '123 Test Street',
        'date_of_birth' => '1990-01-01',
        'joined_date' => '2024-01-01',
        'membership_type' => 'regular',
        'designation' => 'Member',
        'membership_fee' => 1000,
        'status' => 'active'
    ]);

    // Create a test profile picture
    $profilePicture = UploadedFile::fake()->image('profile.jpg');
    $profilePath = $profilePicture->store('profile-pictures/members', 'public');
    $member->profile_picture = $profilePath;

    // Create test attachments
    $attachment1 = UploadedFile::fake()->create('document1.pdf', 100);
    $attachment2 = UploadedFile::fake()->create('document2.pdf', 100);
    $attachmentPaths = [
        $attachment1->store('attachments/members/documents', 'public'),
        $attachment2->store('attachments/members/documents', 'public')
    ];
    $member->attachments = json_encode($attachmentPaths);

    $member->save();
    echo "✓ Member created successfully with ID: {$member->id}\n";

    // Test Read
    echo "\nTesting Member Retrieval...\n";
    $foundMember = Member::find($member->id);
    if ($foundMember) {
        echo "✓ Member found:\n";
        echo "  Name: {$foundMember->first_name} {$foundMember->last_name}\n";
        echo "  NIC: {$foundMember->nic}\n";
        echo "  Membership Type: {$foundMember->membership_type}\n";
    } else {
        throw new Exception("Failed to find member");
    }

    // Test Update
    echo "\nTesting Member Update...\n";
    $foundMember->first_name = 'Jane';
    $foundMember->last_name = 'Smith';
    
    // Update profile picture
    $newProfilePicture = UploadedFile::fake()->image('new_profile.jpg');
    if ($foundMember->profile_picture) {
        Storage::disk('public')->delete($foundMember->profile_picture);
    }
    $newProfilePath = $newProfilePicture->store('profile-pictures/members', 'public');
    $foundMember->profile_picture = $newProfilePath;

    // Update attachments
    $newAttachment = UploadedFile::fake()->create('new_document.pdf', 100);
    $oldAttachments = json_decode($foundMember->attachments, true) ?? [];
    foreach ($oldAttachments as $path) {
        Storage::disk('public')->delete($path);
    }
    $newAttachmentPath = $newAttachment->store('attachments/members/documents', 'public');
    $foundMember->attachments = json_encode([$newAttachmentPath]);

    $foundMember->save();
    echo "✓ Member updated successfully\n";

    // Test Delete
    echo "\nTesting Member Deletion...\n";
    // Delete files first
    if ($foundMember->profile_picture) {
        Storage::disk('public')->delete($foundMember->profile_picture);
    }
    $attachments = json_decode($foundMember->attachments, true) ?? [];
    foreach ($attachments as $path) {
        Storage::disk('public')->delete($path);
    }
    
    $foundMember->delete();
    echo "✓ Member deleted successfully\n";

    // Verify deletion
    $deletedMember = Member::find($member->id);
    if (!$deletedMember) {
        echo "✓ Verified member no longer exists in database\n";
    } else {
        throw new Exception("Member still exists after deletion");
    }

    echo "\nAll tests completed successfully! ✨\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 