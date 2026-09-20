<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Traits\WorksWithDefaultValidations;

class IndexRequest extends FormRequest
{
    use WorksWithDefaultValidations;

    public function rules(): array
    {
        return [
            ...$this->optionalPagination(),
            ...$this->optionalSearch(),
            ...$this->optionalSort(),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
