<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlatResource extends JsonResource
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
            'name' => $this->name,
            'building_id' => $this->building_id,
            'tenant_id' => $this->tenant_id,
            'house_owner_id' => $this->house_owner_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Include relationship data when loaded
            'building_name' => $this->whenLoaded('building', function () {
                return $this->building->name;
            }),
            'building' => $this->whenLoaded('building'),
            
            'tenant_name' => $this->whenLoaded('tenant', function () {
                return $this->tenant->name;
            }),
            'tenant' => $this->whenLoaded('tenant'),
            
            'house_owner_name' => $this->whenLoaded('houseOwner', function () {
                return $this->houseOwner->name;
            }),
            'house_owner' => $this->whenLoaded('houseOwner'),
        ];
    }
}
