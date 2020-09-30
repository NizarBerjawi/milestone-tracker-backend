<?php

namespace App\Models;

use App\Models\User;
use App\Models\Phone;
use App\Support\Uuid\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'user_id'
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
     * Get the user associated with this profile
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the phones associated with this profile
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phones()
    {
        return $this->hasMany(Phone::class);
    }
}
