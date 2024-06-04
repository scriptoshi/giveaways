<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message extends Model
{
    use SoftDeletes;

    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'messages';

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
        'is_reply' => 'boolean',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'message_id',
        'messageable',
        'message',
        'rating',
        'is_reply'
    ];


    /**

     * Get the replies the message Owns.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'message_id', 'id');
    }

    /**

     * Get the message the message Belongs To.
     */
    public function msg(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'message_id', 'id');
    }

    /**

     * Get the user the message Belongs To.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**

     * Get the messageable the message Belongs To.
     */
    public function messageable(): MorphTo
    {
        return $this->morphTo();
    }
}
