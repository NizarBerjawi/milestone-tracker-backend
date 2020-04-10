<?php

namespace App\Support\Uuid;

use Exception;
use App\Exceptions\EntropyException;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Uuid
{
    /**
     * Number of attempts
     */
    const ATTEMPTS = 10;

    /**
     * Attempt to generate a Uuid for a model
     *
     * @param Model $model
     */
    public static function create(Model $model)
    {
        $attempts = 0;

        while($attempts <= static::ATTEMPTS)
        {
            $uuid = Str::uuid();

            // Check if the Uuid already exists
            $exists = $model->newQueryWithoutScopes()
                ->where($model->getUuidField(), $uuid)
                ->exists();
            
            if (! $exists) {
                return $uuid;
            }

            $attempts++;
        }

        throw new EntropyException('Could not generate a unique Uuid for "'.class_basename($model).'"');
    }
}