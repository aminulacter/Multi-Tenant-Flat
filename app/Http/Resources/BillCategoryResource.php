<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillCategoryResource extends JsonResource
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
            'description' => $this->description,
            'building_owner_id' => $this->building_owner_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Include relationship data when loaded
            'house_owner_name' => $this->whenLoaded('houseOwner', function () {
                return $this->houseOwner->name;
            }),
            'house_owner' => $this->whenLoaded('houseOwner'),
        ];
    }
}
