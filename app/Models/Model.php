<?php

namespace App\Models;

use App\CustomClasses\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
  /**
   * Create a new Eloquent query builder for the model.
   *
   * @param  \Illuminate\Database\Query\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder|static
   */
  public function newEloquentBuilder($query)
  {
    return new Builder($query);
  }
}
