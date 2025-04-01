<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class S3FileService
{
    protected string $disk = 's3';

    private function sliceOldPath(string $path): string
    {
        return parse_url($path, PHP_URL_PATH); // Returns "/products/filename.png"
    }

    /**
     * Upload a file to a specific directory in S3
     */
    public function upload(UploadedFile $file, string $directory = '', ?string $filename = null): string
    {
        // Log the S3 configuration from config/filesystems.php
        $s3Config = config("filesystems.disks.{$this->disk}");
        // Log::info('S3 Disk Configuration', $s3Config);

        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload.');
        }

        $originalNameSlice = explode(".", $file->getClientOriginalName())[0];

        $filename = $filename ?? $originalNameSlice .  "-" . uniqid() . '.' . $file->getClientOriginalExtension();
        $directory = trim($directory, '/');
        $path = $file->store($directory, $this->disk);

        if (!$path) {
            throw new \Exception('File upload failed or returned an empty path.');
        }

        return Storage::disk($this->disk)->url($path);
    }


    /** 
     * Delete a file from S3 by its path
     */
    public function delete(string $path): bool
    {
        $newPath = $this->sliceOldPath($path);

        // if (!Storage::disk($this->disk)->exists($newPath)) {
        //     throw new \Exception("File does not exist at: {$newPath}");
        // }

        return Storage::disk($this->disk)->delete($newPath);
    }

    /**
     * Replace an existing file with a new one
     */
    public function update(UploadedFile $file, string $oldPath, string $directory = '', ?string $filename = null): string
    {
        $this->delete($oldPath);

        return $this->upload($file, $directory, $filename);
    }
}
