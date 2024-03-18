<?php

namespace App\Models;


use App\Traits\HasNonce;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use SWeb3\Utils;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasProfilePhoto;
    use Notifiable;
    use HasNonce;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    use HasFactory;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be cast.
     *
     * @var string
     */
    protected $casts = [
        'langs' => 'array',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'use_multiple_accounts',
        'email_verified_at',
        'last_seen_at',
        'referral',
        'username',
        'nonce',
        'is_admin',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];



    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];




    /**
     * Get the accounts the user Owns.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function accounts()
    {
        return $this->hasMany(Account::class, 'user_id', 'id');
    }

    /**

     * Get the accounts the user Owns.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function account()
    {
        return $this->hasOne(Account::class)->latestOfMany();
    }

    /**
     * Get the launchpads the user Owns.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function slips()
    {
        return $this->hasMany(Slip::class, 'user_id', 'id');
    }



    // users that trust this user
    public function getIsVerifiedAttribute()
    {
        return  $this->email_verified_at && $this->phone_verified_at && $this->document_verified_at && $this->id_verified_at;
    }

    //permission
    public function hasPermission()
    {
        return false;
    }

    //permission
    public function isAdmin()
    {
        $admins = str(config('app.admin'))
            ->explode(',')
            ->map(fn ($address) => Utils::toChecksumAddress($address))
            ->all();
        return $this->accounts()
            ->whereIn('address', $admins)
            ->exists();
    }
}
