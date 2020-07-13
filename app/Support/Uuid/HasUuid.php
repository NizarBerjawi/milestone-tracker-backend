<?php

namespace App\Support\Uuid;

use App\Exceptions\ColumnNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

trait HasUuid
{
    /**
     * The "booting" method of the model
     *
     * @return void
     */
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            $field = $model->getUuidField();
            $table = $model->getTable();

            // Check if the column exists in the database
            if (! Schema::hasColumn($table, $field)) {
                throw new ColumnNotFoundException("Uuid column {$field} not found in {$table} table");
            }

            $model->forceFill([
                $field => Uuid::create($model),
            ]);
        });
    }

    /**
     * Get the uuid field name
     *
     * @return string
     */
    public function getUuidField() : string
    {
        if (method_exists($this, 'uuidField')) {
            return $this->uuidField();
        }

        if (property_exists($this, 'uuidField')) {
            return $this->uuidField;
        }

        return 'id';
    }
}
