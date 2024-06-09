<?php

namespace App\Models;

use App\Notifications\OneTimePassword;
use Qirolab\Laravel\Reactions\Traits\Reacts;
use Qirolab\Laravel\Reactions\Contracts\ReactsInterface;
use App\Traits\HasNonce;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use SWeb3\Utils;

class User extends Authenticatable implements MustVerifyEmail, ReactsInterface
{
    use HasApiTokens;
    use HasProfilePhoto;
    use Notifiable;
    use HasNonce;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    use HasFactory;
    use Reacts;


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
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'user_id', 'id');
    }



    /**
     * Get the reactions the launchpads Has.
     *
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class, 'project_id', 'id');
    }

    /**
     * Get the accounts the user Owns.
     *
     */
    public function connections(): HasMany
    {
        return $this->hasMany(Connection::class, 'user_id', 'id');
    }



    /**

     * Get the accounts the user Owns.
     *
     */
    public function account(): HasOne
    {
        return $this->hasOne(Account::class)->latestOfMany();
    }

    /**

     * Get the accounts the user Owns.
     *
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**

     * Get the accounts the user Owns.
     *
     */
    public function giveaways(): HasManyThrough
    {
        return $this->hasManyThrough(Giveaway::class, Project::class);
    }


    /**

     * Get the accounts the user Owns.
     *
     */
    public function project(): HasOne
    {
        return $this->hasOne(Project::class)->latestOfMany();
    }



    /**

     * Get the accounts the user Owns.
     *
     */
    public function pumps(): HasMany
    {
        return $this->hasMany(Pump::class);
    }




    // users that trust this user
    public function getIsVerifiedAttribute()
    {
        return  $this->email_verified_at;
    }


    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new OneTimePassword);
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

    public function getOtp(): string
    {
        $this->otp = substr(str_shuffle(str_repeat($x = '123456789', ceil(10 / strlen($x)))), 1, 10);
        $this->save();
        return $this->otp;
    }
}
