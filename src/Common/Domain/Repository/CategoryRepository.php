<?php

declare(strict_types=1);

namespace Common\Domain\Repository;

use Common\Domain\Exception\NotFoundException;
use Common\Domain\Exception\StorageException;
use Common\Domain\Model\Category;

interface CategoryRepository
{
    /**
     * @return Category[]
     */
    public function findAll(): array; //todo create CategoryFilter

    /**
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Category;

    /**
     * @param array<string, mixed> $criteria
     *
     * @return Category[]
     */
    public function findBy(array $criteria): array;

    public function findById(string $id): ?Category;

    /**
     * @param array<string, mixed> $data
     *
     * @throws StorageException
     */
    public function create(array $data): Category;

    /**
     * @param array<string, mixed> $data
     *
     * @throws NotFoundException
     * @throws StorageException
     */
    public function update(Category $category, array $data): Category;

    /**
     * @throws NotFoundException
     * @throws StorageException
     */
    public function delete(Category $category): void;
}
