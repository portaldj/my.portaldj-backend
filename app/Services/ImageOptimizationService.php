<?php

namespace App\Services;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    /**
     * Handle the upload, optimization, and resizing of an image.
     *
     * @param UploadedFile $file The uploaded file.
     * @param string $path The storage directory path (e.g., 'profile-photos').
     * @param array $variants Array of variants definitions ['name' => [width, height]].
     *                        Example: ['thumb' => [150, 150], 'medium' => [500, 500]]
     *                        Note: If width or height is null, aspect ratio is preserved.
     * @return array Returns an array of paths for the generated images ['original' => 'path', 'thumb' => 'path', etc.]
     */
    public function handle(UploadedFile $file, string $path, array $variants = []): array
    {
        // Generate a unique base filename
        $hash = Str::random(40);
        $filename = $hash . '.jpg'; // Force JPG

        $paths = [];

        // Ensure directory exists
        // Storage::disk('public')->makeDirectory($path); // Image save usually handles this, but good practice.

        // 1. Process Original (Optimized default)
        // We limit the "original" to a sane max size (e.g. 1920x1920) to prevent massive raw files.
        $image = Image::read($file);

        // Resize if too huge (downscale only)
        $image->scaleDown(width: 1920, height: 1920);

        // Encode to JPG with 80% quality
        $encoded = $image->toJpeg(quality: 80);

        // Save original
        $originalPath = "{$path}/{$filename}";
        Storage::disk('r2-public')->put($originalPath, (string) $encoded, 'public');
        $paths['original'] = $originalPath;

        // 2. Process Variants
        foreach ($variants as $key => $dimensions) {
            $width = $dimensions[0] ?? null;
            $height = $dimensions[1] ?? null;

            $variantFilename = "{$hash}_{$key}.jpg";
            $variantPath = "{$path}/{$variantFilename}";

            // clone the original image resource to start fresh for this variant
            $variantImage = Image::read($file); // re-read to avoid compounding quality loss or just clone if possible (v3 uses immutable objects mostly or we can chain off a fresh read)
            // Ideally we use the already read $image but scaling modifies it. 
            // V3: modify updates instance. So best to re-read or clone.
            // Let's re-read for simplicity or clode.
            // $variantImage = clone $image; // If workable.

            if ($width && $height) {
                // Crop/Fit usually best for thumbnails (exact dimensions)
                $variantImage->cover($width, $height);
            } else {
                // Resize preserving aspect ratio (scale)
                $variantImage->scaleDown($width, $height);
            }

            $variantEncoded = $variantImage->toJpeg(quality: 80);
            Storage::disk('r2-public')->put($variantPath, (string) $variantEncoded, 'public');

            $paths[$key] = $variantPath;
        }

        return $paths;
    }

    /**
     * Delete associated images.
     * Expects the original path, attempts to find and delete variants based on naming convention.
     */
    public function delete(string $originalPath, array $variantKeys = [])
    {
        if (Storage::disk('r2-public')->exists($originalPath)) {
            Storage::disk('r2-public')->delete($originalPath);
        }

        // Try access variants
        // original: path/to/hash.jpg
        // variant: path/to/hash_{key}.jpg

        $info = pathinfo($originalPath);
        $dirname = $info['dirname']; // path/to
        $filename = $info['filename']; // hash
        // extension is always jpg in our system now, but check if passed path has it.

        foreach ($variantKeys as $key) {
            $variantPath = "{$dirname}/{$filename}_{$key}.jpg";
            if (Storage::disk('r2-public')->exists($variantPath)) {
                Storage::disk('r2-public')->delete($variantPath);
            }
        }
    }
}
