<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{

    /**
     * Boot function from laravel.
     */
    protected static function bootHasSlug()
    {
        static::created(function ($model) {
            $model->slug .= '-' .  $model->id;
            $model->save();
        });
    }
}
