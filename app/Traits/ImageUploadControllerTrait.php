<?php

namespace App\Traits;

use App\Http\Requests\StorePostImageRequest;
use App\Http\Requests\StoreProfileImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Post;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Gate;

trait ImageUploadControllerTrait
{
    /**
     * @param StoreProfileImageRequest $request
     * @param ImageUploadService $imageUploadService
     * @return ImageResource
     */
    public function storeProfileImage(StoreProfileImageRequest $request, ImageUploadService $imageUploadService): ImageResource
    {
        $image = $imageUploadService->storeProfileImage($request->file('image'), $request->user(), 'profiles');
        return new ImageResource($image);
    }

    /**
     * Almacena una imagen para un post específico.
     */
    public function storeImage(StorePostImageRequest $request, Post $post, ImageUploadService $imageUploadService): ImageResource
    {
        Gate::authorize('update', $post);

        $image = $imageUploadService->store($request->file('image'), $post, 'post_header', 'posts');
        return new ImageResource($image);
    }
}
