<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class ImageService
{
    public static function upload(UploadedFile $file, string $folder = 'images'): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, 'public');
        return $path;
    }

    public static function uploadMultiple(array $files, string $folder = 'images'): array
    {
        $paths = [];
        foreach ($files as $file) {
            $paths[] = self::upload($file, $folder);
        }
        return $paths;
    }

    public static function delete(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}