<?php

require __DIR__.'/../vendor/autoload.php';

// Load environment variables
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Player;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

echo "Starting Player CRUD Test Script...\n\n";

try {
    // Test Create
    echo "Testing Player Creation...\n";
    $player = new Player([
        'first_name' => 'John',
        'last_name' => 'Player',
        'nic' => '123456789012',
        'ffsl_number' => 'FFSL1234',
        'phone' => '1234567890',
        'whatsapp_number' => '1234567890',
        'address' => '123 Test Street',
        'position' => 'striker',
        'jersey_number' => 10,
        'date_of_birth' => '1995-01-01',
        'joined_date' => '2024-01-01',
        'contract_amount' => 100000,
        'contract_start_date' => '2024-01-01',
        'contract_end_date' => '2025-01-01',
        'achievements' => 'Top scorer 2023',
        'status' => 'active'
    ]);

    // Create a test profile picture
    $profilePicture = UploadedFile::fake()->image('profile.jpg');
    $profilePath = $profilePicture->store('profile-pictures/players', 'public');
    $player->profile_picture = $profilePath;

    // Create test attachments
    $attachment1 = UploadedFile::fake()->create('contract.pdf', 100);
    $attachment2 = UploadedFile::fake()->create('medical.pdf', 100);
    $attachmentPaths = [
        $attachment1->store('attachments/players/documents', 'public'),
        $attachment2->store('attachments/players/documents', 'public')
    ];
    $player->attachments = json_encode($attachmentPaths);

    $player->save();
    echo "✓ Player created successfully with ID: {$player->id}\n";

    // Test Read
    echo "\nTesting Player Retrieval...\n";
    $foundPlayer = Player::find($player->id);
    if ($foundPlayer) {
        echo "✓ Player found:\n";
        echo "  Name: {$foundPlayer->first_name} {$foundPlayer->last_name}\n";
        echo "  Position: {$foundPlayer->position}\n";
        echo "  Jersey Number: {$foundPlayer->jersey_number}\n";
        echo "  FFSL Number: {$foundPlayer->ffsl_number}\n";
    } else {
        throw new Exception("Failed to find player");
    }

    // Test Update
    echo "\nTesting Player Update...\n";
    $foundPlayer->first_name = 'Mike';
    $foundPlayer->last_name = 'Striker';
    $foundPlayer->position = 'midfielder';
    $foundPlayer->jersey_number = 11;
    $foundPlayer->contract_amount = 120000;
    
    // Update profile picture
    $newProfilePicture = UploadedFile::fake()->image('new_profile.jpg');
    if ($foundPlayer->profile_picture) {
        Storage::disk('public')->delete($foundPlayer->profile_picture);
    }
    $newProfilePath = $newProfilePicture->store('profile-pictures/players', 'public');
    $foundPlayer->profile_picture = $newProfilePath;

    // Update attachments
    $newAttachment = UploadedFile::fake()->create('new_contract.pdf', 100);
    $oldAttachments = json_decode($foundPlayer->attachments, true) ?? [];
    foreach ($oldAttachments as $path) {
        Storage::disk('public')->delete($path);
    }
    $newAttachmentPath = $newAttachment->store('attachments/players/documents', 'public');
    $foundPlayer->attachments = json_encode([$newAttachmentPath]);

    $foundPlayer->save();
    echo "✓ Player updated successfully\n";

    // Test Delete
    echo "\nTesting Player Deletion...\n";
    // Delete files first
    if ($foundPlayer->profile_picture) {
        Storage::disk('public')->delete($foundPlayer->profile_picture);
    }
    $attachments = json_decode($foundPlayer->attachments, true) ?? [];
    foreach ($attachments as $path) {
        Storage::disk('public')->delete($path);
    }
    
    $foundPlayer->delete();
    echo "✓ Player deleted successfully\n";

    // Verify deletion
    $deletedPlayer = Player::find($player->id);
    if (!$deletedPlayer) {
        echo "✓ Verified player no longer exists in database\n";
    } else {
        throw new Exception("Player still exists after deletion");
    }

    echo "\nAll tests completed successfully! ✨\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 