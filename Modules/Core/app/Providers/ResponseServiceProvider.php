<?php

namespace Modules\Core\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\NewAccessToken;
use Modules\Core\Enums\CrudOperation;
use Modules\Core\Helpers\ResponseHelper;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Response::macro(
            'success',
            function (
                ?string $message = null,
                array|JsonResource|ResourceCollection $data = [],
                array $extraMetaData = [],
                int $statusCode = ResponseAlias::HTTP_OK,
                bool $castToEmptyObject = false,
            ) {
                $message = $message ?? (string) __('response-messages.success.default');

                return ResponseHelper::success(
                    message: $message,
                    data: $data,
                    extraMetaData: $extraMetaData,
                    statusCode: $statusCode,
                    castToEmptyObject: $castToEmptyObject
                );
            }
        );


        Response::macro('crudSuccess', function (
            string $model = '',
            CrudOperation $crudOperation = CrudOperation::STORE,
            array|JsonResource $data = [],
            array $extraMetaData = [],
            int $statusCode = ResponseAlias::HTTP_OK,
        ) {
            $message = __(
                'Core::responses.crud_successes.'.$crudOperation->value,
                ['model' => $model]
            );

            return ResponseHelper::success(
                message: $message,
                data: $data,
                extraMetaData: $extraMetaData,
                statusCode: $statusCode
            );
        });
    }
}
