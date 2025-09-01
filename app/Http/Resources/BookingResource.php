<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Formatea la salida de las solicitudes de Booking
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'guest' => new UserResource($this->whenLoaded('guest')),
            'dj' => new UserResource($this->whenLoaded('dj')),
            'event_details' => $this->event_details,
            'requested_date' => $this->requested_date,
            'status' => $this->status,
        ];
    }
}
