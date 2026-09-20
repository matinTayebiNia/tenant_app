<?php

namespace Modules\Core\Helpers;

use ReflectionClass;

class ConfigHelper
{
    public static $per_page;

    public static $limit_per_page;

    public static $expiration;

    public static $min_age;

    public static $max_age;

    public static $sort_field;

    public function __construct()
    {
        $props = (new ReflectionClass(static::class))
            ->getProperties();

        foreach ($props as $prop) {
            static::${$prop->name} = config('Core.'.$prop->name);
        }
    }
}
