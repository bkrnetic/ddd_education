<?php

declare(strict_types=1);

namespace Common\Infrastructure\Specification;

use Common\Application\Specification\CategoryExistsSpecification;
use Storage\Doctrine\Main\Repository\CategoryRepository;

final class DoctrineCategoryExistsSpecification implements CategoryExistsSpecification
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function satisfiedBy(string $uuid): bool
    {
        $category = $this->categoryRepository->findById($uuid);

        return $category !== null;
    }
}
