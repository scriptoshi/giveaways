<?php



namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class Chain extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => Str::of($this->slug)->camel()->kebab(),
            'type' => $this->type,
            'chainId' => $this->chainId,
            'explorer' => $this->explorer,
            'https' => $this->https,
            'websockets' => $this->websockets,
            'label' => $this->label,
            'active' => $this->active,
            'inactive' => !$this->active,
            'testnet' => $this->testnet,
            'coins' =>  Coin::collection($this->whenLoaded('coins')),
        ];
    }
}
