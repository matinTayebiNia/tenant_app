<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Traits\WorksWithDefaultValidations;

class SearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:1', 'max:255']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
