<?php

namespace Modules\Core\Helpers;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Modules\Core\Enums\PublicFilePath;

class Helper
{
    public static function moduleIsActive(string $moduleName): bool
    {
        return in_array($moduleName, ACTIVE_MODULES);
    }

    public static function getLocalFilePath(PublicFilePath $path, $title, string $relative = ''): false|string
    {
        $path = $path->value.$relative."/$title.";

        return ($files = glob(public_path("$path*")))
            ? config('app.url').$path.pathinfo($files[0])['extension']
            : false;

    }

    public static function getModuleFromClass(string $classNameSpace): string
    {
        ! class_exists($classNameSpace) && throw new InvalidArgumentException;

        preg_match('/Modules[\\\ \/](\w+)/', $classNameSpace, $matches);

        return $matches[1];
    }

    public static function hasTrait(string|object $classNameSpace, array|string $traits): bool
    {
        if (is_string($classNameSpace)) {
            ! class_exists($classNameSpace) && throw new InvalidArgumentException;
        }

        $traits = Arr::wrap($traits);

        return ! array_diff($traits, class_uses($classNameSpace));
    }

    public static function getOrganizationalPhone(string $code, string $phone): string
    {
        return $code.'-'.$phone;
    }

    public static function convert2english($string)
    {
        $newNumbers = range(0, 9);
        // 1. Persian HTML decimal
        $persianDecimal = ['&#1776;', '&#1777;', '&#1778;', '&#1779;', '&#1780;', '&#1781;', '&#1782;', '&#1783;', '&#1784;', '&#1785;'];
        // 2. Arabic HTML decimal
        $arabicDecimal = ['&#1632;', '&#1633;', '&#1634;', '&#1635;', '&#1636;', '&#1637;', '&#1638;', '&#1639;', '&#1640;', '&#1641;'];
        // 3. Arabic Numeric
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        // 4. Persian Numeric
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        $string = str_replace($persianDecimal, $newNumbers, $string);
        $string = str_replace($arabicDecimal, $newNumbers, $string);
        $string = str_replace($arabic, $newNumbers, $string);

        return str_replace($persian, $newNumbers, $string);
    }


    public static function calculateProgress(float|int $previous, float|int $current, int $precision = 2): ?float
    {
        if ($previous == 0) {
            return $current == 0 ? 0.0 : null;
        }

        $change = (($current - $previous) / $previous) * 100;

        return round($change, $precision);
    }
}
