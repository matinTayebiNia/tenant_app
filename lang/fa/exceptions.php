<?php

use Modules\Core\Enums\ExceptionCode;

return [
    'default' => [
        'title' => 'ارور ناشناخته',
        'message' => 'اروری وجود دارد',
        'description' => 'توضیح اضافه ای برای ارور یافت نشد',
    ],
    ExceptionCode::MethodNotAllowedHttpException->value => [
        'message' => 'این نوع درخواست برای این مسیر معتبر نمی باشد',
        'description' => 'ممکن است مشکل از نوع درخواست وارد شده برای این مسیر باشد',
    ],
    ExceptionCode::ValidationException->value => [
        'message' => 'اروری در فیلد های وارد شده وجود دارد',
        'description' => 'یک یا چندین بخش درخواست قابل پذیرش نمی باشد',
    ],
    ExceptionCode::AuthenticationException->value => [
        'message' => 'اروری در احراز هویت رخ داد',
        'description' => 'مشکلی در احراز هویت وجود داشت',
    ],
    ExceptionCode::ModelNotFoundException->value => [
        'message' => 'رکورد یافت نشد',
        'description' => 'رکورد فرستاده شده در درخواست (احتمالا به عنوان پارامتر مسیر) یافت نشد',
    ],
    ExceptionCode::DatabaseException->value => [
        'message' => 'ارور در پایگاه داده',
        'description' => 'ممکن است پایگاه داده مشکلی داشته باشد. لطفا دوباره امتحان کنید',
    ],
    ExceptionCode::ExternalApiException->value => [
        'message' => 'سرویس دهنده خارجی اروری برگردانده است',
        'description' => 'لطفا تنظیمات سرویس دهنده خارجی را بررسی کرده و دوباره امتحان کنید',
    ],

    // generics
    ExceptionCode::GenericNotFoundException->value => [
        'message' => 'این مسیر وجود ندارد',
        'description' => 'لطفا آدرس مسیر را چک و دوباره امتحان کنید',
    ],
    ExceptionCode::GenericTooManyRequestsException->value => [
        'message' => 'تعداد درخواست های بالا در مدت زمان کوتاهی فرستاده شده است',
        'description' => 'کمی منتظر بمانید و دوباره تلاش کنید',
    ],
    ExceptionCode::GenericBadRequestException->value => [
        'message' => 'درخواست قابل پردازش نمیباشد',
        'description' => 'داده فرستاده شده و پارامتر های درخواست را چک و دوباره امتحان کنید',
    ],
    ExceptionCode::GenericForbiddenException->value => [
        'message' => 'اجازه دسترسی به این منبع برای شما وجود ندارد',
        'description' => 'شما به اکانت خود ورود کرده اید اما اجازه دسترسی به این منبع را ندارید',
    ],
    ExceptionCode::GenericInternalServerError->value => [
        'message' => 'اروری در سمت سرور رخ داده است',
        'description' => 'با ادمین های سرور برای اطلاعات بیشتر و بررسی مشکل تماس بگیرید',
    ],
    ExceptionCode::GenericUnauthorizedException->value => [
        'message' => 'ابتدا وارد حساب کاربری خود شوید و سپس دوباره تلاش کنید',
        'description' => 'ابتدا وارد حساب کاربری خود شوید و سپس دوباره تلاش کنید',
    ],

];
