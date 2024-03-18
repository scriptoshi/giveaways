<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Factory extends JsonResource
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
            'foundry' => $this->foundry,
            'version' => $this->version,
            'factory_version' => $this->factory_version,
            'chainId' => (int)$this->chainId,
            'contract' => $this->contract,
            'active' => $this->active,
            'type' => $this->type,
            'name'=>$this->type->name(),
            'deployable'=>$this->type->isDeployable(),
            'isLaunchPad'=>$this->type->isLaunchpad(),
            'isFairLaunch'=>$this->type->isFairLaunch(),
            'factory_abi' => $this->factory_abi,
            'abi' => $this->abi,
        ];
    }
}
