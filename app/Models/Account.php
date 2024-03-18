<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'accounts';
    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['address', 'provider', 'name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public static function scopeAddressExists(Builder $query, $account)
    {
        return $query->where('address', $account)
            ->exists();
    }
}
