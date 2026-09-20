<?php

namespace Modules\Core\ValueObjects;

use Elastic\ScoutDriverPlus\Searchable;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Support\Str;
use Modules\Core\Enums\ExceptionCode;
use Modules\Core\Exceptions\BaseException;
use Modules\Core\Helpers\Helper;

class LandingSearch
{
    public array $fields = ['title'];

    public function __construct(public string $model,
                                public string $title,
                                public array  $query,
                                public string $resource,
                                public int    $priority = 0,
                                public mixed  $condition = null,
                                public array $with = []
    )
    {
        $this->validateModel();
        $this->validateResource();

        // TODO: Model exists with a different config
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'query' => $this->query,
            'priority' => $this->priority,
            'resource' => $this->resource,
            'condition' => $this->condition,
            'with' => $this->with
        ];
    }

    public function validateModel(): void
    {
        if (!class_exists($this->model))
            throw BaseException::new(
                ExceptionCode::GenericInternalServerError,
                message: 'class not exists'
            );

//        if (!Helper::hasTrait($this->model, Searchable::class))
//            throw BaseException::new(
//                ExceptionCode::GenericInternalServerError,
//                message: 'not searchable model. please use searchable trait'
//            );
    }

    public function validateResource(): void
    {
        if (!class_exists($this->resource))
            throw BaseException::new(
                ExceptionCode::GenericInternalServerError,
                message: 'class not exists'
            );
    }

}
