<?php

declare(strict_types=1);

namespace Common\Application\Exception;

use RuntimeException;

final class TranslationException extends RuntimeException
{
    private function __construct(string $message = '', int $code = 409)
    {
        parent::__construct($message, $code);
    }

    public static function becauseUnexpectedTypeProvided(): self
    {
        return new self('Unexpected type provided', 500);
    }
}
