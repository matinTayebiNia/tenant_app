<?php

namespace Modules\Log\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Traits\WorksWithDefaultValidations;
use Modules\Log\Models\ActivityLog;
use Modules\Permission\Support\Services\AuthorizationService;

class IndexLogRequest extends FormRequest
{
    use WorksWithDefaultValidations;

    /**
     * Get the validation rules that apply to the request.
     */
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
        return (bool) app(AuthorizationService::class)
            ->forModel(ActivityLog::class)
            ->allows(user: $this->user(), checkPermissionIds: false);

    }
}
