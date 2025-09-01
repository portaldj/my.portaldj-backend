<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadService
{
    /**
     * @param UploadedFile $file
     * @param Model $model
     * @param string $type
     * @param string $directory
     * @return Image
     */
    public function store(UploadedFile $file, Model $model, string $type, string $directory): Image
    {
        $path = $file->store($directory, 'public');

        return $model->images()->create([
            'path' => $path,
            'type' => $type,
        ]);
    }

    /**
     * @param UploadedFile $file
     * @param Model $user
     * @param string $directory
     * @return Image
     */
    public function storeProfileImage(UploadedFile $file, Model $user, string $directory): Image
    {
        if ($user->profileImage) {
            Storage::disk('public')->delete($user->profileImage->path);
            $user->profileImage()->delete();
        }

        $path = $file->store($directory, 'public');

        return $user->profileImage()->create([
            'path' => $path,
            'type' => 'profile',
        ]);
    }
}
