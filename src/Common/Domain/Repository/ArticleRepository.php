<?php

declare(strict_types=1);

namespace Common\Domain\Repository;

use Common\Domain\Exception\JsonException;
use Common\Domain\Exception\NotFoundException;
use Common\Domain\Exception\StorageException;
use Common\Domain\Model\Article;
use ReflectionException;

interface ArticleRepository
{
    /** @return Article[] */
    public function findAll(): array;

    /**
     * @param array<string, mixed> $criteria
     *
     * @return Article[]
     */
    public function findBy(array $criteria): array;

    /**
     * @param array<string, mixed> $criteria
     *
     * @throws ReflectionException
     */
    public function findOneBy(array $criteria): ?Article;

    /**
     * @throws ReflectionException
     */
    public function findById(string $id): ?Article;

    /**
     * @param array<string, mixed> $data
     *
     * @throws JsonException
     * @throws NotFoundException
     * @throws StorageException
     */
    public function create(array $data): Article;

    /**
     * @param array<string, mixed> $data
     *
     * @throws JsonException
     * @throws NotFoundException
     * @throws StorageException
     */
    public function update(Article $propertyDefinition, array $data): Article;

    /**
     * @throws NotFoundException
     * @throws StorageException
     */
    public function delete(Article $propertyDefinition): void;
}
