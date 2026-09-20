<?php

namespace Modules\Core\Helpers;

use Closure;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexHelper
{
    public static function mapIndex($value, Closure $callback)
    {
        $items = $value->map($callback);

        return $value instanceof LengthAwarePaginator
            ? new LengthAwarePaginator(
                items: $items,
                total: $value->total(),
                perPage: $value->perPage(),
                currentPage: $value->currentPage(),
                options: $value->getOptions(),
            )
            : $items;
    }
}
