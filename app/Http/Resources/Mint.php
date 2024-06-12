<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Mint extends JsonResource
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
            'user_id'=> $this->user_id,
            'owner'=> $this->owner,
            'nft_contract'=> $this->nft_contract,
            'nft'=> $this->nft,
            'tokenId'=> $this->tokenId,
            'txhash'=> $this->txhash,
            'verified'=> $this->verified,
            'user'=> new User($this->whenLoaded('user')),
        ];
    }
}
