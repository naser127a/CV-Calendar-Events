<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'content' => $this->content,
            'image' => $this->image,

            'type' => $this->type,
            'source_type' => $this->source_type,
            'external_id' => $this->external_id,
            'external_url' => $this->external_url,

            'published_at' => $this->published_at,
            'status' => $this->status,

            'is_breaking' => $this->is_breaking,
            'breaking_until' => $this->breaking_until,

            'user' => UserResource::make(
                $this->whenLoaded('user')
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
