<?php

declare(strict_types=1);

namespace Common\Application\ValueObject;

class TranslationTuple
{
    private string $message;
    /** @var array<string, mixed> */
    private array $params;

    /** @param array<string, mixed> $params */
    public function __construct(string $message, array $params = [])
    {
        $this->message = $message;
        $this->params = $params;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /** @return array<string, mixed> */
    public function getParams(): array
    {
        return $this->params;
    }
}
