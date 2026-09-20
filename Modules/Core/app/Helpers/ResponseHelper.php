<?php

namespace Modules\Core\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResponseHelper
{
    public static function success(
        string $message,
        array|JsonResource|ResourceCollection $data = [],
        array $extraMetaData = [],
        ?int $statusCode = Response::HTTP_OK,
        bool $castToEmptyObject = false,
    ): JsonResponse {
        $data = self::render($data);

        if ($castToEmptyObject) {
            $data['res'] = count($data['res']) > 0 ? $data['res'] : (object) null;
        }

        return new JsonResponse([
            'message' => $message,
            'data' => $data['res'],
            ...(
                ($meta = $data['with'])
                    ? ['meta' => key($meta) ? $meta : current($meta)]
                    : []
            ),
            ...(
                ($additional = $data['additional'])
                    ? ['additional' => key($additional) ? $additional : current($additional)]
                    : []
            ),
            ...$extraMetaData,
        ], $statusCode);

    }

    private static function render(array|string|stdClass|JsonResource|Collection|Model $data = []): array
    {
        $with = [];
        $additional = [];
        $res = $data;

        if ($data instanceof JsonResource && $data->resource instanceof LengthAwarePaginator) {

            $res = [
                'paginate' => [
                    'currentPage' => $data->resource->currentPage(),
                    'lastPage' => $data->resource->lastPage(),
                    'total' => $data->resource->total(),
                    'next_page_url' => $data->resource->nextPageUrl(),
                    'prev_page_url' => $data->resource->previousPageUrl(),
                ],
                'data' => $data,
            ];
        }

        if ($data instanceof JsonResource) {
            $with = $data->with(request());
            $additional = $data->additional;
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($value == null) {
                    continue;
                }
                $tmp = self::render($value);
                if ($tmp['with']) {
                    $with[$key] = $tmp['with'];
                }
                if ($tmp['additional']) {
                    $additional[$key] = $tmp['additional'];
                }
            }
        }

        return [
            'with' => $with,
            'additional' => $additional,
            'res' => $res,
        ];
    }

    public static function expectJsonResponse(Request $request, Throwable $e): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }

    public static function paginate(int $total, ?int $perPage)
    {
        $currentPage = Paginator::resolveCurrentPage();
        $prevPage = $currentPage > 1 ? $currentPage - 1 : null;
        $lastIndex = $currentPage * ($perPage ?? ConfigHelper::$per_page);
        $next = $total >= $lastIndex
            ? url()->current().'?'.http_build_query([
                ...\request()->query(), 'page' => $currentPage + 1,
            ])
            : null;

        $prev = $prevPage
            ? url()->current().'?'.http_build_query([
                ...\request()->query(), 'page' => $prevPage,
            ])
            : null;

        $lastPage = is_int($res = $total / ($perPage->per_page ?? ConfigHelper::$per_page))
            ? $res
            : floor($res) + 1;

        return [
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
            'total' => $total,
            'next_page_url' => $next,
            'prev_page_url' => $prev,
        ];

    }
}
