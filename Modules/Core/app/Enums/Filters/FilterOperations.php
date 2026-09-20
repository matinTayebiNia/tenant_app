<?php

namespace Modules\Core\Enums\Filters;

use Modules\Core\Traits\HasEnumValues;

enum FilterOperations: string
{
    use HasEnumValues;

    case Equal = '=';

    case Range = '<>';

    case Lesser = '<';

    case Greater = '>';
    case Like = 'like';

    case LessThanOrEqual = '<=';

}
