<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "avatar" => $this->avatar,
            "phone" => $this->phone,
            "address" => $this->address,
            'role' => [
                'id' => $this->role?->id,
                'name' => $this->role?->name,
                'display_name' => $this->role?->display_name,
            ],
            "last_name" => $this->last_name,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
