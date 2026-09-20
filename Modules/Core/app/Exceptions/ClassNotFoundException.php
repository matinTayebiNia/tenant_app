<?php

namespace Modules\Core\Exceptions;

class ClassNotFoundException extends BaseException
{
    protected static $statusCode = 500;
}
