<?php

namespace BaseCode\Common\Models;

use Illuminate\Database\Eloquent\Model;

// https://stackoverflow.com/questions/27204485/synchronizing-a-one-to-many-relationship-in-laravel
abstract class BaseModel extends Model
{
    /**
     * Overrides the default Eloquent hasMany relationship to return a HasManySyncable.
     *
     * {@inheritDoc}
     * @return \BaseCode\Common\HasManySyncable
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return new HasManySyncable(
            $instance->newQuery(),
            $this,
            $instance->getTable() . '.' . $foreignKey,
            $localKey
        );
    }
}
