<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Facades\Storage;

trait AssertsCrudResponses
{
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