<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Launchpad extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $endDate = $this->created_at->addDays(10);
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'decimals' => 18, // hardcoded in contract
            'chainId' => $this->chainId,
            'address' => $this->address,
            'contract' => $this->contract,
            'abi' => $this->abi,
            //time
            'startTime' => $this->created_at,
            'startTimeFormatted' => $this->created_at->toDateTimeString(),
            'startTimeTs' => $this->created_at->timestamp,
            'hasStarted' => now()->gt($this->created_at),
            'endTime' =>  $endDate,
            'endTimeFormatted' =>  $endDate->toDateTimeString(),
            'hasEnded' =>  now()->gt($endDate),
            'isToday' => $endDate->isToday(),
            'timeAgo' => $endDate->diffForHumans(),
            'endTimeTs' =>  $endDate->timestamp,
            // relations
            'project' => new Project($this->whenLoaded('project')),
            'uploads' => Upload::collection($this->whenLoaded('uploads')),
            'logo' => new Upload($this->whenLoaded('logo')),
        ];
    }
}
