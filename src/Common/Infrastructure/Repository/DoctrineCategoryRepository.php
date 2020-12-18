<?php

declare(strict_types=1);

namespace Common\Infrastructure\Repository;

use Common\Domain\Exception\NotFoundException;
use Common\Domain\Exception\StorageException;
use Common\Domain\Model\Category;
use Common\Domain\Repository\CategoryRepository;
use Common\Infrastructure\Mapper\CategoryMapper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Storage\Doctrine\Main\Entity\Category as StorageCategory;
use Storage\Doctrine\Main\Repository\CategoryRepository as StorageCategoryRepository;
use Symfony\Component\Security\Core\Security;

class DoctrineCategoryRepository implements CategoryRepository
{
    private Security $security;
    private EntityManagerInterface $em;
    private StorageCategoryRepository $storageCategoryRepository;
    private CategoryMapper $categoryMapper;

    public function __construct(
        Security $security,
        EntityManagerInterface $em,
        StorageCategoryRepository $storageCategoryRepository,
        CategoryMapper $categoryMapper
    ) {
        $this->security = $security;
        $this->em = $em;
        $this->storageCategoryRepository = $storageCategoryRepository;
        $this->categoryMapper = $categoryMapper;
    }

    /** {@inheritdoc} */
    public function findAll(): array
    {
        //todo refactor this when filter and sort will be applied
        $categories = $this->storageCategoryRepository->findAll(false);

        return array_map([$this->categoryMapper, 'mapToDomainObject'], $categories);
    }

    /** {@inheritdoc} */
    public function findOneBy(array $criteria): ?Category
    {
        /** @var StorageCategory $storageCategory */
        $storageCategory = $this->storageCategoryRepository->findOneBy($criteria, null, false);
        if (empty($storageCategory)) {
            return null;
        }

        return $this->categoryMapper->mapToDomainObject($storageCategory);
    }

    /** {@inheritdoc} */
    public function findBy(array $criteria): array
    {
        $storageCategorys = $this->storageCategoryRepository->findBy($criteria);

        return array_map([$this->categoryMapper, 'mapToDomainObject'], $storageCategorys);
    }

    /** {@inheritdoc} */
    public function findById(string $id): ?Category
    {
        /** @var StorageCategory $storageCategory */
        $storageCategory = $this->storageCategoryRepository->findById($id, false);
        if (empty($storageCategory)) {
            return null;
        }

        return $this->categoryMapper->mapToDomainObject($storageCategory);
    }

    /** {@inheritdoc} */
    public function create(array $data): Category
    {
        try {
            /** @var StorageCategory $storageCategory */
            $storageCategory = (new StorageCategory())
                ->setName($data['name'])
                ->setCreatedBy($this->security->getUser());

            $this->storageCategoryRepository->save($storageCategory);
        } catch (OptimisticLockException | ORMException $e) {
            throw StorageException::becauseUnexpectedErrorOccurred($e->getMessage());
        }

        return $this->categoryMapper->mapToDomainObject($storageCategory);
    }

    /** {@inheritdoc} */
    public function update(Category $category, array $data): Category
    {
        try {
            /** @var StorageCategory|null $storageCategory */
            $storageCategory = $this->storageCategoryRepository->findById($category->getId(), false);

            if (null === $storageCategory) {
                throw NotFoundException::becauseEntityNotFound(StorageCategory::class, $category->getId());
            }

            if (\array_key_exists('name', $data)) {
                $storageCategory->setName($data['name']);
            }

            $this->storageCategoryRepository->save($storageCategory);
        } catch (OptimisticLockException | ORMException | \ReflectionException $e) {
            throw StorageException::becauseUnexpectedErrorOccurred($e->getMessage());
        }

        return $this->categoryMapper->mapToDomainObject($storageCategory);
    }

    /** {@inheritdoc} */
    public function delete(Category $category): void
    {
        try {
            /** @var StorageCategory|null $storageCategory */
            $storageCategory = $this->storageCategoryRepository->findById($category->getId(), true);

            if (null === $storageCategory) {
                throw NotFoundException::becauseEntityAlreadyDeleted(StorageCategory::class, $category->getId());
            }

//            uncomment this is articles need to be removed manually
//            $storageCategory->getArticles()->clear();
//            $this->storageCategoryRepository->save($storageCategory);

            $this->storageCategoryRepository->remove($storageCategory);
        } catch (OptimisticLockException | ORMException | \ReflectionException $e) {
            throw StorageException::becauseUnexpectedErrorOccurred($e->getMessage());
        }
    }

    private function buildCategoryResultSetMapping(): ResultSetMapping
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(StorageCategory::class, 'ad');
        $rsm->addFieldResult('ad', 'id', 'id');
        $rsm->addMetaResult('ad', 'created_by_user_id', 'created_by_user_id');
        $rsm->addFieldResult('ad', 'name', 'name');
        $rsm->addFieldResult('ad', 'slug', 'slug');
        $rsm->addFieldResult('ad', 'created_at', 'createdAt');
        $rsm->addFieldResult('ad', 'updated_at', 'updatedAt');
        $rsm->addFieldResult('ad', 'deleted_at', 'deletedAt');

        return $rsm;
    }
}
