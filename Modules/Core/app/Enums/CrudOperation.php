<?php

namespace Modules\Core\Enums;

enum CrudOperation: string
{
    case STORE = 'stored';
    case UPDATE = 'updated';
    case DESTROY = 'destroyed';
}
