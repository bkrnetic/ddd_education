<?php

declare(strict_types=1);

namespace Common\Application\Validation;

use Common\Application\Exception\TranslationException;
use Common\Application\Specification\CategoryExistsSpecification;
use Common\Domain\Model\Article;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleValidator
{
    private TranslatorInterface $translator;
    private RequestParamValidator $requestParamValidator;
    private CategoryExistsSpecification $categoryExistsSpecification;

    public function __construct(
        TranslatorInterface $translator,
        RequestParamValidator $requestParamValidator,
        CategoryExistsSpecification $categoryExistsSpecification
    ) {
        $this->translator = $translator;
        $this->requestParamValidator = $requestParamValidator;
        $this->categoryExistsSpecification = $categoryExistsSpecification;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws TranslationException
     */
    public function validateOnCreate(array $data): Result
    {
        $errors = array_merge(
            [],
            $this->validateName($data),
            $this->validateContent($data),
            $this->validatePublished($data),
            $this->validateCategory($data),
        );

        return new Result($errors);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws TranslationException
     */
    public function validateOnUpdate(array $data, Article $ignoreModel): Result
    {
        $errors = array_merge(
            [],
            $this->validateName($data),
            $this->validateContent($data),
            $this->validatePublished($data),
            $this->validateCategory($data),
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

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    private function validateContent(array $data, bool $required = true): array
    {
        $errors = $this->requestParamValidator->validateRequired($data, 'content', $required);
        if (empty($errors) === false) {
            return $errors;
        }

        $errors = $this->requestParamValidator->validateString($data, 'content');
        if (empty($errors) === false) {
            return $errors;
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    private function validatePublished(array $data, bool $required = true): array
    {
        $errors = $this->requestParamValidator->validateRequired($data, 'published', $required);
        if (empty($errors) === false) {
            return $errors;
        }

        $errors = $this->requestParamValidator->validateBoolean($data, 'published');
        if (empty($errors) === false) {
            return $errors;
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function validateCategory(array $data): array
    {
        if (!isset($data['categoryId'])) {
            return ['categoryId' => $this->translator->trans('This field is required')];
        }

        $errors = $this->requestParamValidator->validateUuid($data, 'categoryId');
        if (!empty($errors)) {
            return $errors;
        }

        if (!$this->categoryExistsSpecification->satisfiedBy($data['categoryId'])) {
            return ['categoryId' => $this->translator->trans('Category with ID = %id% not found', ['%id%' => $data['categoryId']])];
        }

        return [];
    }
}
