<?php

declare(strict_types=1);

namespace Common\Application\Validation;

use Common\Application\Helper\RegexHelper;
use Ramsey\Uuid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestParamValidator
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    public function validateRequired(array $data, string $key, bool $required = true): array
    {
        /*
         * Using empty is risky because it will return true if numeric value is 0
         * which is not something we want. Therefore it is used in lazy evaluation with isset.
         */
        if ($required === true && !\array_key_exists($key, $data) && !isset($data[$key])) {
            return [$key => $this->translator->trans('This field is required')];
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    public function validateString(array $data, string $key): array
    {
        if (isset($data[$key]) && (!\is_string($data[$key]) || trim($data[$key]) === '')) {
            return [$key => $this->translator->trans('Please provide a valid, non-empty string value')];
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    public function validateNumeric(array $data, string $key): array
    {
        if (isset($data[$key]) && (!is_numeric($data[$key]) || \is_string($data[$key]))) {
            return [$key => $this->translator->trans('Please provide a valid float value')];
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    public function validateDateTime(array $data, string $key): array
    {
        if (isset($data[$key])) {
            if (!\is_string($data[$key]) || trim($data[$key]) === '') {
                return [$key => $this->translator->trans('This field cannot be empty')];
            }
            if (!preg_match(RegexHelper::getDateTimeExpression(), $data[$key])) {
                return [$key => $this->translator->trans('Please provide a valid date in YYYY-MM-DD or YYYY-MM-DD H:i:s format')];
            }
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    public function validateInteger(array $data, string $key): array
    {
        if (isset($data[$key]) && \is_int($data[$key]) === false) {
            return [$key => $this->translator->trans('Please provide a valid integer value')];
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    public function validateBoolean(array $data, string $key)
    {
        if (isset($data[$key]) && \is_bool($data[$key]) === false) {
            return [$key => $this->translator->trans('Please provide a valid boolean (true/false) value')];
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    public function validateUuid(array $data, string $key)
    {
        if (isset($data[$key]) && (!\is_string($data[$key]) || Uuid::isValid($data[$key]) === false)) {
            return [$key => $this->translator->trans('Please provide a valid identifier (uuid) value')];
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    public function validateArrayContainsStringValuesOnly($data, string $key)
    {
        if (isset($data[$key])) {
            foreach ($data[$key] as $value) {
                if (!\is_string($value)) {
                    return [$key => $this->translator->trans('Array must contain string values only')];
                }
            }
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    public function validateArrayContainsUuidsOnly($data, string $key)
    {
        if (isset($data[$key])) {
            foreach ($data[$key] as $value) {
                if (!\is_string($value) || Uuid::isValid($value) === false) {
                    return [$key => $this->translator->trans('Array must contain UUID strings only')];
                }
            }
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    public function validateArrayContainsIntegersOnly($data, string $key)
    {
        if (isset($data[$key])) {
            foreach ($data[$key] as $value) {
                if (\is_int($value) === false) {
                    return [$key => $this->translator->trans('Array must contain integer values only')];
                }
            }
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    public function validateCamelCase($data, string $key)
    {
        if (isset($data[$key]) && !@preg_match('/^[a-z]+([a-zA-Z]+)*$/', $data[$key])) {
            return [$key => $this->translator->trans('Please provide a valid camelCase string')];
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    public function validateKebabCase($data, string $key)
    {
        if (isset($data[$key]) && !@preg_match('/^[a-z]+(-[a-z]+)*$/', $data[$key])) {
            return [$key => $this->translator->trans('Please provide a valid kebab-case string')];
        }

        return [];
    }
}
