<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel
{
    /**
     * Guarded attributes for mass assignment.
     *
     * @var array
     */
    protected $guarded = [];
}
