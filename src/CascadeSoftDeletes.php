<?php

namespace Iatstuti\Database\Support;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relation;

trait CascadeSoftDeletes
{
    /**
     * Boot the trait.
     *
     * Listen for the deleting event of a soft deleting model, and run
     * the delete operation for any configured relationship methods.
     *
     * @throws \RuntimeException
     */
    protected static function bootCascadeSoftDeletes()
    {
        static::deleting(function ($model) {
            if (! $model->implementsSoftDeletes()) {
                throw new RuntimeException(sprintf(
                    '%s does not implement Illuminate\Database\Eloquent\SoftDeletes',
                    get_called_class()
                ));
            }

            foreach ($model->cascadeDeletes as $relationship) {
                if (! $model->{$relationship}() instanceof Relation) {
                    throw new LogicException(sprintf(
                        'Relationship [%s] must return an object of type Illuminate\Database\Eloquent\SoftDeletes',
                        $relationship
                    ));
                }

                $model->{$relationship}()->delete();
            }
        });
    }


    /**
     * Determine if the current model implements soft deletes.
     *
     * @return bool
     */
    protected function implementsSoftDeletes()
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this));
    }

}
