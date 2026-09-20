<?php

namespace Modules\Stock\Enums;

enum StockType: string
{

    case In = 'in';
    case Out = 'out';

    case Transfer = 'transfer';


    public function title(): ?string
    {
        return match ($this) {
            self::In => __('ورود'),
            self::Out => __('خروج'),
            self::Transfer => __('انتقال')
        };
    }

}
