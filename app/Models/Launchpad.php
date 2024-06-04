<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class Launchpad extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'launchpads';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be cast to native types.
     *
     * @var string
     */
    protected $casts = [
        'abi' => 'array',
    ];


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'name',
        'symbol',
        'chainId',
        'address',
        'contract',
        'lastblock',
        'abi'
    ];


    /**

     * Get the project the launchpad Belongs To.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    /**

     * Get the project the launchpad Belongs To.
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class, 'launchpad_id', 'id');
    }

    /**

     * Get the uploads the launchpad Belongs To.
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    /**

     * Get the logo the launchpad Belongs To.
     */
    public function logo(): MorphOne
    {
        return $this->morphOne(Upload::class, 'uploadable')->where('key', 'logo');
    }
}
