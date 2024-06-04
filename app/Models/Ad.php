<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Enums\AdCategory;
use App\Enums\Period;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Ad extends Model
{
    use SoftDeletes;
    use HasUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ads';

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
        'verified_at' => 'datetime',
        'tags' => 'array',
        'promoted_at' => 'datetime',
        'category' => AdCategory::class,
        'period' => Period::class,
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'uuid',
        'category',
        'tags',
        'slug',
        'title',
        'description',
        'price',
        'duration',
        'period',
        'rating',
        'telegram',
        'url',
        'verified_at',
        'promoted_at'
    ];


    /**

     * Get the user the ad Belongs To.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**

     * Get the escrows the ad Owns.
     */
    public function escrows(): HasMany
    {
        return $this->hasMany(Escrow::class, 'ad_id', 'id');
    }

    /**

     * Get the messages the ad Belongs To.
     */
    public function messages(): MorphMany
    {
        return $this->morphMany(Message::class, 'messageable');
    }

    /**

     * Get the uploads the ad Belongs To.
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    /**

     * Get the image the ad Belongs To.
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Upload::class, 'uploadable')->where('key', 'image');
    }
}
