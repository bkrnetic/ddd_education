<?php

declare(strict_types=1);

namespace Common\Domain\Exception;

use ReflectionClass;
use ReflectionException;
use RuntimeException;

final class NotFoundException extends RuntimeException
{
    private function __construct(string $message = '')
    {
        parent::__construct($message, 404);
    }

    /**
     * @param int|string|null $id
     * @param class-string $className
     *
     * @throws ReflectionException
     */
    public static function becauseEntityNotFound(string $className, $id = null): self
    {
        /** @var class-string $shortClassName */
        $shortClassName = (new ReflectionClass($className))->getShortName();

        if ($id !== null) {
            return new self(sprintf('%s with ID = %s not found', $shortClassName, $id));
        }

        return new self(sprintf('%s not found', $shortClassName));
    }

    /**
     * @param int|string $id
     * @param class-string $className
     *
     * @throws ReflectionException
     */
    public static function becauseEntityAlreadyDeleted(string $className, $id): self
    {
        $shortClassName = (new ReflectionClass($className))->getShortName();

        return new self(sprintf('%s with ID = %s is already deleted', $shortClassName, $id));
    }

    public static function becauseInvalidSlug(string $slug): self
    {
        return new self(sprintf('No asset for slug `%s` found', $slug));
    }
}
