<?php

namespace Modules\Core\Enums\Filters;

enum FilterMethods: string
{
    case Where = 'where';

    case WhereIn = 'whereIn';

    case WhereBetween = 'whereBetween';
}
