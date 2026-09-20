<?php

namespace Modules\Core\Exceptions;

use Modules\Core\Exceptions\BaseException;

class AliasExistsException extends BaseException
{
    protected static $statusCode = 500;
}
