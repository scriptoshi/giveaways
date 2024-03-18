<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{

    /**
     * Boot function from laravel.
     */
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            if (empty($model->uuid))
                $model->uuid = Str::uuid()->toString();
        });
    }
}
