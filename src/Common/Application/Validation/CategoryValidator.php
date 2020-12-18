<?php

declare(strict_types=1);

namespace Common\Application\Validation;

use Common\Domain\Model\Category;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryValidator
{
    private RequestParamValidator $requestParamValidator;
    private TranslatorInterface $translator;

    public function __construct(
        RequestParamValidator $requestParamValidator,
        TranslatorInterface $translator
    ) {
        $this->requestParamValidator = $requestParamValidator;
        $this->translator = $translator;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function validateOnCreate(array $data): Result
    {
        $errors = array_merge(
            [],
            $this->validateName($data),
        );

        return new Result($errors);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function validateOnUpdate(array $data, Category $ignoreModel): Result
    {
        $errors = array_merge(
            [],
            $this->validateName($data),
        );

        return new Result($errors);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    private function validateName(array $data, bool $required = true): array
    {
        $errors = $this->requestParamValidator->validateRequired($data, 'name', $required);
        if (empty($errors) === false) {
            return $errors;
        }

        $errors = $this->requestParamValidator->validateString($data, 'name');
        if (empty($errors) === false) {
            return $errors;
        }

        return [];
    }
}
