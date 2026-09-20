<?php

namespace Modules\Core\Traits;

trait HasEnumValues
{
    public static function getOnlyValues(): array
    {
        return array_reduce(self::cases(), function ($carry, $item) {
            $carry[] = $item->value;

            return $carry;
        }, []);
    }

    public static function fromName(string $name): ?\BackedEnum
    {
        foreach (self::cases() as $status) {
            if ($name == $status->name) {
                return $status;
            }
        }

        return null;
    }

    public static function getOnlyNames(): array
    {
        return array_column(self::cases(), 'name');
    }
}
