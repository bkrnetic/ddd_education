<?php

declare(strict_types=1);

namespace Common\Domain\Exception;

final class StorageException extends \Exception
{
    private function __construct(string $message = '', int $code = 500)
    {
        parent::__construct($message, $code);
    }

    public static function becauseUnexpectedErrorOccurred(string $message): self
    {
        return new self($message);
    }

    public static function becauseHasChildren(): self
    {
        return new self('The entity cannot be deleted. Please make sure to remove its children first', 405);
    }
}
