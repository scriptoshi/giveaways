<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;

trait WhenMorphed
{
    public function whenMorphed($relationship)
    {
        return $this->whenLoaded($relationship, function () use ($relationship) {
            $morphType = $relationship . '_type';
            $morphAlias = $this->resource->$morphType;
            $morphResourceClass = "App\\Http\\Resources\\".class_basename($morphAlias);
            return new $morphResourceClass($this->resource->{$relationship});
        });
    }
}
