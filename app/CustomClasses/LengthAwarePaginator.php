<?php

namespace App\CustomClasses;

use Illuminate\Pagination\LengthAwarePaginator as PaginationLengthAwarePagination;
use Illuminate\Contracts\Support\Arrayable;

class LengthAwarePaginator extends PaginationLengthAwarePagination implements Arrayable
{
  public function toArray()
  {
    $key = $this->getOptions()['keyBy'];
    return [
      "{$key}s" => $this->items->pluck($key),
      'data' => $this->items->keyBy($key),
      'current_page' => $this->currentPage(),
      'last_page' => $this->lastPage(),
      'per_page' => $this->perPage(),
      'from' => $this->firstItem(),
      'to' => $this->lastItem(),
      'total' => $this->total(),
      'first_page_url' => $this->url(1),
      'last_page_url' => $this->url($this->lastPage()),
      'next_page_url' => $this->nextPageUrl(),
      'prev_page_url' => $this->previousPageUrl(),
      'path' => $this->path(),
    ];
  }
}
