<?php

declare(strict_types=1);

namespace Common\Infrastructure\Repository;

use Common\Domain\Exception\NotFoundException;
use Common\Domain\Exception\StorageException;
use Common\Domain\Model\Article;
use Common\Domain\Repository\ArticleRepository;
use Common\Infrastructure\Mapper\ArticleMapper;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Storage\Doctrine\Main\Entity\Article as StorageArticle;
use Storage\Doctrine\Main\Entity\Category as StorageCategory;
use Storage\Doctrine\Main\Repository\ArticleRepository as StorageArticleRepository;
use Storage\Doctrine\Main\Repository\CategoryRepository as StorageCategoryRepository;

class DoctrineArticleRepository implements ArticleRepository
{
    private StorageArticleRepository $storageArticleRepository;
    private ArticleMapper $articleMapper;
    private StorageCategoryRepository $storageCategoryRepository;

    public function __construct(
        StorageArticleRepository $storageArticleRepository,
        ArticleMapper $articleMapper,
        StorageCategoryRepository $storageCategoryRepository
    ) {
        $this->storageArticleRepository = $storageArticleRepository;
        $this->articleMapper = $articleMapper;
        $this->storageCategoryRepository = $storageCategoryRepository;
    }

    /** {@inheritdoc} */
    public function findAll(): array
    {
        //todo refactor this when filter and sort will be applied
        $articles = $this->storageArticleRepository->findAll(false);

        return array_map([$this->articleMapper, 'mapToDomainObject'], $articles);
    }

    /** {@inheritdoc} */
    public function findBy(array $criteria): array
    {
        $storageAssets = $this->storageArticleRepository->findBy($criteria);

        return array_map([$this->articleMapper, 'mapToDomainObject'], $storageAssets);
    }

    /** {@inheritdoc} */
    public function findOneBy(array $criteria): ?Article
    {
        /** @var StorageArticle $storageArticle */
        $storageArticle = $this->storageArticleRepository->findOneBy($criteria, null, false);
        if (empty($storageArticle)) {
            return null;
        }

        return $this->articleMapper->mapToDomainObject($storageArticle);
    }

    /** {@inheritdoc} */
    public function findById(string $id): ?Article
    {
        /** @var StorageArticle $storageArticle */
        $storageArticle = $this->storageArticleRepository->findById($id, false);
        if (empty($storageArticle)) {
            return null;
        }

        return $this->articleMapper->mapToDomainObject($storageArticle);
    }

    /** {@inheritdoc} */
    public function create(array $data): Article
    {
        try {
            /** @var StorageCategory|null $storageCategory */
            $storageCategory = $this->storageCategoryRepository->findById($data['categoryId']);

            if (null === $storageCategory) {
                throw NotFoundException::becauseEntityNotFound(StorageCategory::class, $data['categoryId']);
            }

            /** @var StorageArticle $storageArticle */
            $storageArticle = (new StorageArticle())
                ->setName($data['name'])
                ->setContent($data['content'])
                ->setPublished($data['published'])
                ->setCategory($storageCategory);

            $this->storageArticleRepository->save($storageArticle);
        } catch (OptimisticLockException | ORMException | \ReflectionException $e) {
            throw StorageException::becauseUnexpectedErrorOccurred($e->getMessage());
        }

        return $this->articleMapper->mapToDomainObject($storageArticle);
    }

    /** {@inheritdoc} */
    public function update(Article $article, array $data): Article
    {
        try {
            /** @var StorageArticle|null $storageArticle */
            $storageArticle = $this->storageArticleRepository->findById($article->getId(), false);

            if (null === $storageArticle) {
                throw NotFoundException::becauseEntityNotFound(StorageArticle::class, $article->getId());
            }

            if (\array_key_exists('name', $data)) {
                $storageArticle->setName($data['name']);
            }

            if (\array_key_exists('content', $data)) {
                $storageArticle->setContent($data['content']);
            }

            if (\array_key_exists('published', $data)) {
                $storageArticle->setPublished($data['published']);
            }

            if (\array_key_exists('category', $data)) {
                /** @var StorageCategory|null $storageCategory */
                $storageCategory = $this->storageCategoryRepository->findById($data['categoryId']);

                if (null === $storageCategory) {
                    throw NotFoundException::becauseEntityNotFound(StorageCategory::class, $data['categoryId']);
                }

                $storageArticle->setCategory($storageCategory);
            }

            $this->storageArticleRepository->save($storageArticle);
        } catch (OptimisticLockException | ORMException | \ReflectionException $e) {
            throw StorageException::becauseUnexpectedErrorOccurred($e->getMessage());
        }

        return $this->articleMapper->mapToDomainObject($storageArticle);
    }

    /** {@inheritdoc} */
    public function delete(Article $article): void
    {
        try {
            /** @var StorageArticle|null $storageArticle */
            $storageArticle = $this->storageArticleRepository->findById($article->getId(), true);

            if (null === $storageArticle) {
                throw NotFoundException::becauseEntityAlreadyDeleted(StorageArticle::class, $article->getId());
            }

            $this->storageArticleRepository->remove($storageArticle);
        } catch (OptimisticLockException | ORMException | \ReflectionException $e) {
            throw StorageException::becauseUnexpectedErrorOccurred($e->getMessage());
        }
    }
}
