<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \App\Enums\QuestType;
use \App\Enums\QuestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quest extends Model
{
    use SoftDeletes;

    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'quests';

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
        'type' => QuestType::class,
        'status' => QuestStatus::class,
        'data' => 'array',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'giveaway_id',
        'user_id',
        'connection_id',
        'username',
        'groupId',
        'type',
        'status',
        'data',
        'min',
        'sleep'
    ];


    /**

     * Get the giveaway the quest Belongs To.
     */
    public function giveaway(): BelongsTo
    {
        return $this->belongsTo(Giveaway::class, 'giveaway_id', 'id');
    }




    /**

     * Get the giveaway the quest Belongs To.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'quest_id', 'id');
    }

    /**

     * Get the user the quest Belongs To.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
