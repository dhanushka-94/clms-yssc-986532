<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasAttachments
{
    /**
     * Store a new file attachment
     *
     * @param UploadedFile $file
     * @param string $type Type of attachment (e.g., 'documents', 'images')
     * @return string|null The path where the file was stored, or null if failed
     */
    public function storeAttachment(UploadedFile $file, string $type = 'documents'): ?string
    {
        try {
            // Generate a unique filename
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            
            // Create the storage path
            $path = "attachments/{$this->getTable()}/{$this->id}/{$type}";
            
            // Store the file
            $filePath = $file->storeAs($path, $filename, 'public');
            
            return $filePath;
        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            return null;
        }
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
                $path = $this->storeAttachment($file, $type);
                if ($path) {
                    $paths[] = $path;
                }
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
        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }
            return false;
        } catch (\Exception $e) {
            \Log::error('File deletion failed: ' . $e->getMessage());
            return false;
        }
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
        try {
            $path = "attachments/{$this->getTable()}/{$this->id}";
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->deleteDirectory($path);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to delete all attachments: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all attachments for this model
     *
     * @param string $type Type of attachment (e.g., 'documents', 'images')
     * @return array
     */
    public function getAttachments(string $type = null): array
    {
        try {
            $path = "attachments/{$this->getTable()}/{$this->id}";
            if ($type) {
                $path .= "/{$type}";
            }
            
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->files($path);
            }
            return [];
        } catch (\Exception $e) {
            \Log::error('Failed to get attachments: ' . $e->getMessage());
            return [];
        }
    }
} 