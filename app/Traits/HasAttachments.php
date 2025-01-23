<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasAttachments
{
    /**
     * Store a new file attachment
     *
     * @param UploadedFile $file
     * @param string $type Type of attachment (e.g., 'documents', 'images')
     * @return string The path where the file was stored
     */
    public function storeAttachment(UploadedFile $file, string $type = 'documents'): string
    {
        $path = $file->store("attachments/{$this->getTable()}/{$this->id}/{$type}", 'public');
        return $path;
    }

    /**
     * Store multiple file attachments
     *
     * @param array $files Array of UploadedFile
     * @param string $type Type of attachment (e.g., 'documents', 'images')
     * @return array Array of stored file paths
     */
    public function storeAttachments(array $files, string $type = 'documents'): array
    {
        $paths = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $this->storeAttachment($file, $type);
            }
        }
        return $paths;
    }

    /**
     * Delete a file attachment
     *
     * @param string $path Path to the file
     * @return bool
     */
    public function deleteAttachment(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Delete multiple file attachments
     *
     * @param array $paths Array of file paths
     * @return void
     */
    public function deleteAttachments(array $paths): void
    {
        foreach ($paths as $path) {
            $this->deleteAttachment($path);
        }
    }

    /**
     * Delete all attachments for this model
     *
     * @return bool
     */
    public function deleteAllAttachments(): bool
    {
        return Storage::disk('public')->deleteDirectory("attachments/{$this->getTable()}/{$this->id}");
    }

    /**
     * Get all attachments for this model
     *
     * @param string $type Type of attachment (e.g., 'documents', 'images')
     * @return array
     */
    public function getAttachments(string $type = null): array
    {
        $path = "attachments/{$this->getTable()}/{$this->id}";
        if ($type) {
            $path .= "/{$type}";
        }
        
        return Storage::disk('public')->files($path);
    }
} 