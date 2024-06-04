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
            'id' => $this->id,
            'nfts_id' => $this->nfts_id,
            'owner' => $this->owner,
            'tokenId' => $this->tokenId,
            'txhash' => $this->txhash,
            'nft' => new Nft($this->whenLoaded('nft')),
        ];
    }
}
