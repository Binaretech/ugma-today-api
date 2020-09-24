<?php

namespace App\CustomClasses;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use App\CustomClasses\LengthAwarePaginator;
use Illuminate\Container\Container;

class Builder extends EloquentBuilder
{

  /**
   * Paginate the given query.
   *
   * @param  int|null  $perPage
   * @param  string $keyBy
   * @param  array  $columns
   * @param  string  $pageName
   * @param  int|null  $page
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
   *
   * @throws \InvalidArgumentException
   */
  public function paginate($perPage = null, $keyBy = 'id', $columns = ['*'], $pageName = 'page', $page = null)
  {
    $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

    $perPage = $perPage ?: $this->model->getPerPage();

    $results = ($total = $this->toBase()->getCountForPagination())
      ? $this->forPage($page, $perPage)->get($columns)
      : $this->model->newCollection();

    return $this->paginator($results, $total, $perPage, $page, [
      'path' => LengthAwarePaginator::resolveCurrentPath(),
      'pageName' => $pageName,
      'keyBy' => $keyBy,
    ]);
  }


  /**
   * Create a new length-aware paginator instance.
   *
   * @param  \Illuminate\Support\Collection  $items
   * @param  int  $total
   * @param  int  $perPage
   * @param  int  $currentPage
   * @param  array  $options
   * @return \Illuminate\Pagination\LengthAwarePaginator
   */
  protected function paginator($items, $total, $perPage, $currentPage, $options)
  {
    return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
      'items',
      'total',
      'perPage',
      'currentPage',
      'options'
    ));
  }
}
