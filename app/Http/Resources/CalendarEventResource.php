<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'end_at' => $this->end_at,
            'status' => $this->status,
            'start_at' => $this->start_at,
            'color' => $this->color,
            'all_day' => $this->all_day,
            'creator' => UserResource::make(
                $this->whenLoaded('creator')
            )
        ];
    }
}
