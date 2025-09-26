<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'role_name' => $this->whenLoaded('role', fn() => $this->role->name),
            'house_owner' => $this->whenLoaded('houseOwner', fn() => [
                'id' => $this->houseOwner->id,
                'name' => $this->houseOwner->name,
                'email' => $this->houseOwner->email,
                'address' => $this->houseOwner->address,
                'city' => $this->houseOwner->city,
                'zip' => $this->houseOwner->zip,
            ]),
            'tenant' => $this->whenLoaded('tenant', fn() => [
                'id' => $this->tenant->id,
                'name' => $this->tenant->name,
                'email' => $this->tenant->email,
                'address' => $this->tenant->address,
                'city' => $this->tenant->city,
                'zip' => $this->tenant->zip,
                'house_owner_id' => $this->tenant->house_owner_id,
            ]),
        ];
    }
}
