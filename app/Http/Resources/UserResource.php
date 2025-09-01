<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Para perfiles públicos y datos generales de usuario.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dj_name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'profile' => [
                'city' => $this->whenLoaded('city', fn() => $this->city->name),
                'country' => $this->whenLoaded('city', fn() => $this->city->country->name),
                'profile_types' => ProfileTypeResource::collection($this->whenLoaded('profileTypes')),
                'skills' => SkillResource::collection($this->whenLoaded('skills')),
                'genres' => MusicGenreResource::collection($this->whenLoaded('musicGenres')),
            ],
            'profile_image_url' => $this->whenLoaded('profileImage', fn() => asset('storage/' . $this->profileImage->path)),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
