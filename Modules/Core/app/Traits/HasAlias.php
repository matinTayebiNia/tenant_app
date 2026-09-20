<?php

namespace Modules\Core\Traits;


use Modules\Core\Enums\ExceptionCode;
use Modules\Core\Exceptions\AliasExistsException;

trait HasAlias
{
    /**
     * @throws AliasExistsException
     */
    public static function bindAlias(?string $subAlias = null): void
    {
        app()->alias(static::class, static::getAlias());
    }

    /**
     * @throws AliasExistsException
     */
    public static function getAlias(): string
    {
        $cn = class_basename(static::class);

        if (! app()->isAlias($cn) || app()->getAlias($cn) === strtolower($cn)) {
            return strtolower($cn);
        }

        throw AliasExistsException::new(ExceptionCode::GenericInternalServerError);
    }
}
