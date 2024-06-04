<?php

/** dev:
 *Stephen Isaac:  ofuzak@gmail.com.
 *Skype: ofuzak
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \App\Enums\ConnectionProvider;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Connection extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'connections';

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
        'provider' => ConnectionProvider::class,
        'expires_at' => 'datetime'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'userId',
        'email',
        'provider',
        'followers',
        'following',
        'tweets',
        'verified',
        'description',
        'username',
        'token',
        'refreshToken',
        'expiresIn',
        'expires_at',
    ];

    /**

     * Get the user the connection Belongs To.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeTelegram($query)
    {
        return $query->where('provider', ConnectionProvider::TELEGRAM);
    }
}
