<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Project extends Model
{
    use SoftDeletes;
    use HasUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects';

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
        'promoted_at' => 'datetime',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'uuid',
        'name',
        'slug',
        'description',
        'rank',
        'verified_at',
        'promoted_at'
    ];


    /**

     * Get the user the project Belongs To.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**

     * Get the giveaways the project Owns.
     */
    public function giveaways(): HasMany
    {
        return $this->hasMany(Giveaway::class, 'project_id', 'id');
    }

    /**

     * Get the questers the project Owns.
     */
    public function questers(): HasManyThrough
    {
        return $this->hasManyThrough(Quester::class, Giveaway::class);
    }

    /**
     * Get the questers the project Owns.
     */
    public function quests(): HasManyThrough
    {
        return $this->hasManyThrough(Quest::class, Giveaway::class);
    }

    /**
     * Get the questers the project Owns.
     */
    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(Task::class, Giveaway::class);
    }
    /**

     * Get the nfts the project Owns.
     */
    public function nfts(): HasManyThrough
    {
        return $this->hasManyThrough(Nft::class, Giveaway::class);
    }

    /**

     * Get the launchpad the project Owns.
     */
    public function launchpad(): HasOne
    {
        return $this->hasOne(Launchpad::class, 'project_id', 'id');
    }

    /**

     * Get the uploads the project Belongs To.
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }
    /**

     * Get the uploads the project Belongs To.
     */
    public function socials(): MorphMany
    {
        return $this->morphMany(Social::class, 'socialable');
    }

    /**

     * Get the logo the project Belongs To.
     */
    public function logo(): MorphOne
    {
        return $this->morphOne(Upload::class, 'uploadable')->where('key', 'logo');
    }
}
