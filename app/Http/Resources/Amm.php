<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Amm extends JsonResource
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
            'id'=>$this->id,
            'chain_id'=> $this->chain_id,
            'chainId'=> $this->chainId,
            'name'=> $this->name,
            'url'=> $this->url,
            'info_url'=> $this->info_url,
            'logo_uri'=> $this->logo_uri,
            'token_url'=> $this->token_url,
            'pair_url'=> $this->pair_url,
            'router'=> $this->router,
            'factory'=> $this->factory,
            'status'=> $this->status,
            'inactive'=> !$this->status,
            'busy'=> false,
            'chain'=> new Chain($this->whenLoaded('chain')),
        ];
    }
}
