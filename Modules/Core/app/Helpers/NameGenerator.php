<?php

namespace Modules\Core\Helpers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NameGenerator
{
    public static function byMicrotime(): string
    {
        return preg_replace('/[^0-9]/', '', microtime());
    }

    public static function byHash(string $name = '', array|string $specialChar = ['/', '.']): string
    {
        return Str::remove($specialChar, Hash::make($name ?: self::byMicrotime()));
    }
}
