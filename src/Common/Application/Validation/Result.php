<?php

namespace Common\Application\Validation;

final class Result
{
    /** @var array<string, mixed> */
    private array $errorMap;

    private bool $isValid;

    /** @param array<string, mixed> $errorMap */
    public function __construct(array $errorMap = [])
    {
        $this->errorMap = $errorMap;
        $this->isValid = \count($errorMap) === 0;
    }

    /** @return array<string, mixed> */
    public function getErrorMap(): array
    {
        return $this->errorMap;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }
}
