<?php

namespace App\Models;

use App\Enums\GiveawayStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \App\Enums\Period;
use \App\Enums\GiveawayType;
use Envatic\CrudStrap\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Giveaway extends Model
{
    use SoftDeletes;

    use HasUuid;
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'giveaways';

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
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'live' => 'boolean',
        'period' => Period::class,
        'type' => GiveawayType::class,
        'status' => GiveawayStatus::class,
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'uuid',
        'slug',
        'brief',
        'duration',
        'period',
        'starts_at',
        'ends_at',
        'prize',
        'fee',
        'sleep',
        'sleep_balance',
        'symbol',
        'hash',
        'status',
        'errors',
        'chainId',
        'account',
        'num_winners',
        'num_claimed',
        'type',
        'draw_size',
        'live'
    ];


    /**

     * Get the project the giveaway Belongs To.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    /**

     * Get the questers the giveaway Owns.
     */
    public function questers(): HasMany
    {
        return $this->hasMany(Quester::class, 'giveaway_id', 'id');
    }

    /**

     * Get the questers the giveaway Owns.
     */
    public function pumps(): HasManyThrough
    {
        return $this->hasManyThrough(Pump::class, Quester::class);
    }
    /**

     * Get the questers the giveaway Owns.
     */
    public function quests(): HasMany
    {
        return $this->hasMany(Quest::class, 'giveaway_id', 'id');
    }
    /**

     * Get the questers the giveaway Owns.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'giveaway_id', 'id');
    }

    // helpers

    public function started(): bool
    {
        return  now()->gt($this->starts_at);
    }
    // helpers

    public function ended(): bool
    {
        return  now()->gt($this->ends_at);
    }
}
