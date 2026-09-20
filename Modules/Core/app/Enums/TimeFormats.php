<?php

namespace Modules\Core\Enums;

enum TimeFormats: string
{
    case DATE = 'Y-m-d';
    case DATE_TIME = 'Y-m-d H:i:s';

    case TIME = 'H:i:s';

    case YEAR = 'Y';

    public const TIME_VALUE = ' 00:00:00';
}
