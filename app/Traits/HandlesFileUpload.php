<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesFileUpload
{
    public function uploadFile(UploadedFile $file, string $path = "uploads"): ?string

    {
        return $file->store($path, "public");
    }

    public function deleteFile(string $filePath): void
    {
        if ($filePath && Storage::disk("public")->exists($filePath)) {
            Storage::disk("public")->delete($filePath);
        }
    }

    public function replaceFile(
        string $oldFile,
        UploadedFile $newFile,
        string $path
        = "uploads"
    ): ?string {
        if ($oldFile) {
            $this->deleteFile($oldFile);
        }
        return $this->uploadFile($newFile, $path);
    }
}
