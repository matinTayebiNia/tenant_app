<?php

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Enums\RegexPattern;
use Illuminate\Validation\Rule;
use Modules\Core\Helpers\ConfigHelper;
use Ramsey\Collection\Sort;

trait WorksWithDefaultValidations
{
    const MULTI_FIELD_DELIMITER = '_';

    public function optionalPagination(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'gt:0', 'lt:' . ConfigHelper::$limit_per_page],
            'take' => ['sometimes', 'integer', 'gt:0', 'lt:' . ConfigHelper::$limit_per_page],
        ];
    }

    public function optionalSearch(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function optionalSort(): array
    {
        return [
            'sort' => ['sometimes', 'string', Rule::enum(Sort::class)],
        ];
    }

    public function optionalIncludeIds(): array
    {
        return [
            'include_ids' => [
                'sometimes',
                'array'
            ],
            'include_ids.*' => [
                'integer'
            ]
        ];
    }

    public function optionalExcludeIds(): array
    {
        return [
            'exclude_ids' => [
                'sometimes',
                'array'
            ],
            'exclude_ids.*' => [
                'integer'
            ]
        ];
    }

    public function MergeRouteParameterInRequestValidation(string $key, array $validation): array
    {
        $this->merge([$key => $this->route($key)]);

        return [
            $key => $validation
        ];
    }

    public function prepareDelimitedFields($keys = ['include_ids', 'exclude_ids']): void
    {
        foreach ($keys as $key) {
            if (!empty($this->input($key)) && !is_array($this->input($key)) && preg_match(RegexPattern::DELIMITED_IDS->value, $this->input($key))) {
                $this->merge([
                    $key => array_map(fn($id) => (int)$id, explode(self::MULTI_FIELD_DELIMITER, $this->input($key))),
                ]);
            }
        }
    }

    public function optionalMultiFields(
        array  $fields,
        string $each = 'integer'
    ): array
    {
        $rules = [];
        foreach ($fields as $key => $model) {
            $rules[$key] = ['array'];
            $rules[$key . '.*'] = [$each, 'gt:0'];

            if (class_exists($model) && is_subclass_of($model, Model::class)) {
                $lastId = $model::latest('id')->value('id');
                $rules["{$key}.*"][] = "lte:$lastId";
            }
        }

        return $rules;
    }
}
