<?php

declare(strict_types=1);

namespace Common\Infrastructure\Mapper;

use Common\Domain\Model\Article;
use Storage\Doctrine\Main\Entity\Article as StorageArticle;

class ArticleMapper
{
    public function mapToDomainObject(StorageArticle $storageArticle): Article
    {
        $configuration = null;

        return new Article(
            $storageArticle->getId(),
            $storageArticle->getName(),
            $storageArticle->getContent(),
            $storageArticle->isPublished()
        );
    }
}
