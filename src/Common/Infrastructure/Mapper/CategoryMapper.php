<?php

declare(strict_types=1);

namespace Common\Infrastructure\Mapper;

use Common\Domain\Model\Category;
use Storage\Doctrine\Main\Entity\Category as StorageCategory;

class CategoryMapper
{
    private ArticleMapper $articleMapper;

    public function __construct(ArticleMapper $articleMapper)
    {
        $this->articleMapper = $articleMapper;
    }

    public function mapToDomainObject(StorageCategory $storageCategory): Category
    {
        return new Category(
            $storageCategory->getId(),
            $storageCategory->getName(),
            array_map([$this->articleMapper, 'mapToDomainObject'], $storageCategory->getArticles()->toArray())
        );
    }
}
