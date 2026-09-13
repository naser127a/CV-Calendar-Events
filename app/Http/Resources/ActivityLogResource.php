<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'log_name' => $this->log_name,

            'event' => $this->event,

            'description' => $this->description,

            'properties' => $this->properties,

            'causer' => $this->whenLoaded(
                'causer',
                fn () => $this->causer
                    ? UserResource::make($this->causer)
                    : null
            ),

            'subject' => $this->whenLoaded(
                'subject',
                fn () => $this->subject
            ),

            'created_at' => $this->created_at,
        ];
    }
}
