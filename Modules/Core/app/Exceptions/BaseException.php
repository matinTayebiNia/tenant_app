<?php

namespace Modules\Core\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Enums\ExceptionCode;
use Modules\Core\Support\Logger;

class BaseException extends Exception
{
    protected ExceptionCode $exceptionCode;

    protected string $description;

    protected array $extraParams = [];

    public static function new(
        ExceptionCode $exceptionCode,
        array $extraParams = [],
        ?string $message = null,
        ?string $description = null,
        ?int $statusCode = null
    ): static {
        $exception = new static(
            $message ?? $exceptionCode->getMessage(),
            $statusCode ?? $exceptionCode->getCodeStatus()
        );

        $exception->exceptionCode = $exceptionCode;
        $exception->description = $description ?? $exceptionCode->getDescription();
        $exception->extraParams = $extraParams ?: $exception->extraParams;

        return $exception;
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'title' => $this->exceptionCode->getTitle($this->code),
            'exception_code' => $this->exceptionCode->value,
            'message' => $this->getMessage(),
            'description' => $this->description,
            'status' => $this->code,
            ...$this->extraParams,
        ], $this->code);
    }

    public function report(): void
    {
        Logger::logException($this);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getExceptionCode(): int
    {
        return $this->exceptionCode->value;
    }
}
