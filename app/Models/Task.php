<?php

namespace App\Models;

use App\Enums\QuestType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Enums\TaskType;
use App\Enums\TaskStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use SoftDeletes;
    use HasUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tasks';

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
        'status' => TaskStatus::class,
        'validated' => 'boolean'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'giveaway_id',
        'quest_id',
        'quester_id',
        'user_id',
        'uuid',
        'type',
        'status',
        'response',
        'gas',
        'validated'
    ];


    /**

     * Get the giveaway the task Belongs To.
     */
    public function giveaway(): BelongsTo
    {
        return $this->belongsTo(Giveaway::class, 'giveaway_id', 'id');
    }

    /**

     * Get the quest the task Belongs To.
     */
    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class, 'quest_id', 'id');
    }

    /**

     * Get the quester the task Belongs To.
     */
    public function quester(): BelongsTo
    {
        return $this->belongsTo(Quester::class, 'quester_id', 'id');
    }

    /**

     * Get the user the task Belongs To.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
