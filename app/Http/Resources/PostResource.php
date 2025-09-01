<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Formatea la salida de los Posts
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'author' => new UserResource($this->whenLoaded('user')),
            'image_url' => $this->whenLoaded('image', fn() => asset('storage/' . $this->image->path)),
            'comments_count' => $this->comments_count ?? $this->comments->count(),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'posted_at' => $this->created_at->diffForHumans(),
        ];
    }
}
