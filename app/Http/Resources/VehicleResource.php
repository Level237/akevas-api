<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\QuarterResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "vehicle_model"=>$this->vehicle_model,
            "vehicle_number"=>$this->vehicle_number,
            "vehicle_state"=>$this->vehicle_state,
            "vehicle_type"=>$this->vehicle_type,
            "quarters"=>QuarterResource::collection($this->quarters),
            "vehicle_image"=>URL("/storage/".$this->vehicle_image),
        ];
    }
}
