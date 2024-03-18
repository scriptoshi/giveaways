<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Badge extends Model
{
    use SoftDeletes;

    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'badges';

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
        'show' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'badge',
        'description',
        'show',
        'active'
    ];



    /**
     * Get the token the badge Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function tokens()
    {
        return $this->morphedByMany(Token::class, 'badgeable');
    }
}
