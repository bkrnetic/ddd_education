<?php

namespace Common\Domain\Enum;

use ReflectionClass;
use ReflectionException;

abstract class BaseEnum
{
    /** @var string[] */
    protected static array $labels = [];

    /**
     * @return string[]
     *
     * @throws ReflectionException
     */
    public static function getConstants(): array
    {
        $oClass = new ReflectionClass(static::class);

        return $oClass->getConstants();
    }

    public static function getLabelById(string $id): ?string
    {
        if (isset(static::$labels[$id])) {
            return static::$labels[$id];
        }

        return null;
    }

    /**
     * @return array<int|string, array<string, string>|string>
     *
     * @throws ReflectionException
     */
    public static function getOptions(bool $objects = false): array
    {
        $constants = self::getConstants();
        $keys = array_values($constants);

        $return = [];

        foreach ($keys as $key) {
            $value = self::getLabelById($key);
            if (empty($value)) {
                continue;
            }

            if ($objects) {
                $return[] = ['name' => $value, 'id' => $key];
                continue;
            }

            $return[$value] = $key;
        }

        return $return;
    }

    /**
     * @param mixed $value
     *
     * @throws ReflectionException
     */
    public static function contains($value): bool
    {
        $values = self::getConstants();

        return true === \in_array($value, $values);
    }

    /**
     * @return string[]
     */
    public static function getLabels(): array
    {
        return static::$labels;
    }
}
