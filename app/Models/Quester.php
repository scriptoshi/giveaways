<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \App\Enums\QuesterStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quester extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'questers';

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
        'status' => QuesterStatus::class,
        'completed_at' => 'datetime',
        'sleep_claimed_at' => 'datetime',
        'boosted_at' => 'datetime',
        'last_pump_at' => 'datetime',
        'claim' => 'array',
        'sleep_claim' => 'array',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'giveaway_id',
        'user_id',
        'percent',
        'qid',
        'uuid',
        'address',
        'pump',
        'sleep',
        'status',
        'comment',
        'signature',
        'claim',
        'sleep_signature',
        'sleep_hash',
        'sleep_claim',
        'sleep_claimed_at',
        'completed_at',
        'boosted_at',
        'last_pump_at'
    ];


    /**

     * Get the giveaway the quester Belongs To.
     */
    public function giveaway(): BelongsTo
    {
        return $this->belongsTo(Giveaway::class, 'giveaway_id', 'id');
    }

    /**

     * Get the user the quester Belongs To.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**

     * Get the pumps the quester Owns.
     */
    public function pumps(): HasMany
    {
        return $this->hasMany(Pump::class, 'quester_id', 'id');
    }

    /**

     * Get the questers who boosted using this qid
     */
    public function questers(): BelongsToMany
    {
        return $this->belongsToMany(Quester::class, 'boost_quester', 'boost_id', 'quester_id');
    }


    /**

     * Get the pumps the quester Owns.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'quester_id', 'id');
    }
}
