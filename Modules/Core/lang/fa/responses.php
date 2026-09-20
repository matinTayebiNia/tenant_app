<?php

use Modules\Core\Enums\CrudOperation;

return [
    'crud_successes' => [
        CrudOperation::STORE->value => 'اطلاعات :model مورد نظر با موفقیت ذخیره شد',
        CrudOperation::UPDATE->value => 'اطلاعات :model مورد نظر با موفقیت آپدیت شد',
        CrudOperation::DESTROY->value => 'اطلاعات :model مورد نظر با موفقیت حذف شد',
    ],
    'bad_request' => 'user agent  کاربر ست نشده.',
];
