<?php

declare(strict_types=1);

namespace Common\Domain\Exception;

final class JsonException extends \Exception
{
    private function __construct(string $message = '')
    {
        parent::__construct($message);
    }

    public static function becauseInvalidJson(): self
    {
        return new self(json_last_error_msg());
    }
}
