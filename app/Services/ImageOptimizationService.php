<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageOptimizationService
{
    /**
     * Automatically optimizes, resizes, and securely stores an uploaded image.
     *
     * @param UploadedFile $file The uploaded image file from the request
     * @param string $folder The target folder inside the storage disk
     * @param string $disk The storage disk ('local' for private vault, 'public' if non-sensitive)
     * @param int $maxWidth Maximum width to scale down
     * @param int $maxHeight Maximum height to scale down
     * @param int $quality JPEG quality (default 80)
     * @return string Stored relative path
     */
    public static function optimizeAndStore(
        UploadedFile $file,
        string $folder = 'private_log_photos',
        string $disk = 'local',
        int $maxWidth = 1200,
        int $maxHeight = 1200,
        int $quality = 80
    ): string {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());

        // Proportionally downscale large images to reduce payload while preserving visual clarity
        $image->scaleDown(width: $maxWidth, height: $maxHeight);

        // Encode as storage-efficient JPEG
        $encoded = $image->toJpeg($quality);

        // Ensure unique filename
        $filename = trim($folder, '/') . '/' . Str::random(40) . '.jpg';

        Storage::disk($disk)->put($filename, (string) $encoded);

        return $filename;
    }
}
