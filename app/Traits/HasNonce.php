<?php

namespace App\Traits;
use Illuminate\Support\Str;
trait HasNonce
{

    /**
     * Boot function from laravel.
     */
    public function getNonce()
    {
        $this->nonce = Str::random(16);
        $this->save();
        return $this->nonce;
    }
}
