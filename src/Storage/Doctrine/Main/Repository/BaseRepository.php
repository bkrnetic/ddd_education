<?php

declare(strict_types=1);

namespace Storage\Doctrine\Main\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RuntimeException;
use Storage\Doctrine\Main\Entity\StorageEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class BaseRepository extends EntityRepository implements ServiceEntityRepositoryInterface
{
    abstract protected function getEntityClassName(): string;

    public function __construct(EntityManagerInterface $entityManager, ?ClassMetadata $metadata = null, ?ManagerRegistry $registry = null)
    {
        if ($this->getEntityClassName() === '') {
            throw new RuntimeException('Repository entity class name is empty');
        }

        if ($registry) {
            /** @var EntityManager $manager */
            $manager = $registry->getManagerForClass($this->getEntityClassName());

            parent::__construct($manager, $manager->getClassMetadata($this->getEntityClassName()));
        } elseif ($entityManager instanceof EntityManager && $metadata instanceof ClassMetadata) {
            parent::__construct($entityManager, $metadata);
        } else {
            throw new RuntimeException('Failed to initialize repository.');
        }
    }

    /**
     * @return StorageEntity[]
     */
    public function findAll(bool $includeSoftDeletedRecords = false): array
    {
        if ($includeSoftDeletedRecords && $this->isSoftDeletedFilterEnabled()) {
            $this->_em->getFilters()->disable('soft_deletable');
        }

        return parent::findAll();
    }

    /**
     * @param int|string $id
     */
    public function findById($id, bool $includeSoftDeletedRecords = false): ?StorageEntity
    {
        if ($includeSoftDeletedRecords && $this->isSoftDeletedFilterEnabled()) {
            $this->_em->getFilters()->disable('soft_deletable');
        }

        /** @var StorageEntity|null $entity */
        $entity = $this->find($id);

        return $entity;
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, mixed>|null $orderBy
     *
     * @return StorageEntity[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, bool $includeSoftDeletedRecords = false): array
    {
        if ($includeSoftDeletedRecords && $this->isSoftDeletedFilterEnabled()) {
            $this->_em->getFilters()->disable('soft_deletable');
        }

        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, mixed>|null $orderBy
     */
    public function findOneBy(array $criteria, array $orderBy = null, bool $includeSoftDeletedRecords = false)
    {
        if ($includeSoftDeletedRecords && $this->isSoftDeletedFilterEnabled()) {
            $this->_em->getFilters()->disable('soft_deletable');
        }

        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(StorageEntity $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function flush(): void
    {
        $this->_em->flush();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(StorageEntity $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findOr404(int $id, bool $includeSoftDeletedRecords = false): StorageEntity
    {
        if ($includeSoftDeletedRecords && $this->isSoftDeletedFilterEnabled()) {
            $this->_em->getFilters()->disable('soft_deletable');
        }

        /** @var StorageEntity|null $entity */
        $entity = $this->findById($id);

        if (null === $entity) {
            $message = sprintf('Resource of type %s and ID %s could not be found!', $this->getEntityClassName(), $id);
            throw new NotFoundHttpException($message, null, Response::HTTP_NOT_FOUND);
        }

        return $entity;
    }

    private function isSoftDeletedFilterEnabled(): bool
    {
        if ($this->_em->getFilters()->isEnabled('soft_deletable')) {
            return true;
        }

        return false;
    }
}
