<?php

namespace Modules\Core\lang\fa;

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
    ExceptionCode::FileNotFound->value => [
        'message' => 'این فایل وجود ندارد.',
        'description' => 'این فایل یا فایل ها وجود ندارند!!',
    ],
    ExceptionCode::DirectoryNotFound->value => [
        'message' => 'این پوشه وجود ندارد.',
        'description' => 'این پوشه یا پوشه ها وجود ندارند!!',
    ],
    ExceptionCode::DirectoryExists->value => [
        'message' => 'پوشه ای با این نام وجود دارد.',
        'description' => 'پوشه یا پوشه های درخواست شده وجود دارند و امکان جایگزینی آنها وجود ندارد!!',
    ],
    ExceptionCode::FileExists->value => [
        'message' => 'این فایل وجود دارد.',
        'description' => 'این فایل یا فایل ها وجود دارند و امکان جایگزینی آنها وجود ندارد!!',
    ],
    ExceptionCode::WrongPasswordForZipFile->value => [
        'message' => 'رمز عبور فایل زیپ مورد نظر اشتباه است.',
        'description' => 'رمز عبور فرستاده شده برای این فایل درست نمیباشد!!',
    ],
    ExceptionCode::ZipFileCouldNotBeOpened->value => [
        'message' => 'این فایل زیپ به درستی باز نشد.',
        'description' => 'مشکلی در فایل زیپ وجود دارد که مانع باز شدن آن میشود!!',
    ],
    ExceptionCode::NoPasswordProvidedForZipFile->value => [
        'message' => 'فایل زیپ نیاز به رمز عبور دارد.',
        'description' => 'باز کردن این فایل زیپ نیاز به رمز عبور دارد ولی رمزی فرستاده نشده است!!',
    ],
    ExceptionCode::PrivateFileLinkIsNotAvailable->value => [
        'message' => 'فایل های محرمانه لینک عمومی ندارند.',
        'description' => 'امکان نمایش فایل های محرمانه به صورت لینک عمومی وجود ندارد!!',
    ],
    ExceptionCode::NotAvailableForPublicDisks->value => [
        'message' => 'این قابلیت تنها برای دیسک های خصوصی وجود دارد.',
        'description' => 'قابلیتی که سعی در استفاده از آن دارید تنها از دیسک های خصوصی پشتیبانی میکند!!',
    ],
    ExceptionCode::TempUrlAlreadyExists->value => [
        'message' => 'یک لینک موقت برای این فایل از قبل موجود است.',
        'description' => 'این فایل دارای یک لینک موقت می باشد.',
    ],
    ExceptionCode::TempUrlLinkExpired->value => [
        'message' => 'این لینک منقضی شده است.',
        'description' => 'یا تعداد بازدید ها یا زمان این لینک گذشته است!!',
    ],
    ExceptionCode::TempUrlCreationFailed->value => [
        'message' => 'ساخت لینک موقت موفقیت آمیز نبود.',
        'description' => 'مشکلی در سرور برای ایجاد لینک موقت به وجود آمد.',
    ],
    ExceptionCode::TempUrlUpdateFailed->value => [
        'message' => 'ادیت لینک موقت موفقیت آمیز نبود.',
        'description' => 'مشکلی در سرور برای ادیت لینک موقت به وجود آمد.',
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
        'description' => 'دسترسی شما به درخواست داده شده مجاز نمیباشد',
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
