<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Formatea la salida de la agenda.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_name' => $this->event_name,
            'club' => new ClubResource($this->whenLoaded('club')),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];
    }
}
