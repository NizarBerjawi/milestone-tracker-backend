<?php

namespace App\Models;

use App\Support\Uuid\HasUuid;
use App\Notifications\VerifyEmail;
use App\Models\Profile;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasUuid, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates the name of the field that will be a uuid.
     *
     * @var string
     */
    protected $uuidField = 'id';

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification() {
        $this->notify(new VerifyEmail());
    }

    /**
     * Get the profile associated with this user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Check if the user has a profile
     * 
     * @return boolean
     */
    public function hasProfile()
    {
        return $this->profile()->exists();
    }

    /**
     * Check if a user owns a specific profile
     */
    public function owns(Profile $profile)
    {
        return $this->profile->is($profile);
    }
}
