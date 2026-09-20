<?php

namespace Modules\Core\DTOs;

use BackedEnum;
use Illuminate\Database\Eloquent\Model;

abstract readonly class PublishableDto
{
    private string $enum;

    public function setStatus(BackedEnum $statusInput): BackedEnum
    {
        return $statusInput == $this->enum::Scheduled
            ? $this->enum::Active
            : $statusInput;
    }

    public function setPublishedAt(BackedEnum $statusInput, ?string $publishedAt, ?Model $model = null): ?string
    {
        return match ($statusInput) {
            $this->enum::Active =>
            $model && !$publishedAt && $model->published_at < now() //update to active status
                ? ($model->published_at ?? now()->toDateTimeString())
                : now()->toDateTimeString(),
            $this->enum::InActive => $model?->published_at,
            default => $publishedAt ?? $model?->published_at ?? now()->toDateTimeString()
        };
    }

    public function setEnumStatus(string $enumStatusClass): void
    {
        $this->enum = $enumStatusClass;
    }
}
