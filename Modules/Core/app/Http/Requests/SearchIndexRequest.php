<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchIndexRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:1', 'max:255'],
            ...$this->optionalPagination(),
            ...$this->optionalSort(),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
