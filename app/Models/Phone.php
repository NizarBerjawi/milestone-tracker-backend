<?php

namespace App\Models;

use App\Support\Uuid\HasUuid;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'type'
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
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
