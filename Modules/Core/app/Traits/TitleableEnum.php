<?php

namespace Modules\Core\Traits;

use Modules\Core\Helpers\Helper;

trait TitleableEnum
{
    public function title(): string
    {
        $module = Helper::getModuleFromClass(self::class);

        return __("{$module}::enum." . self::class . "." . $this->value);
    }
}
