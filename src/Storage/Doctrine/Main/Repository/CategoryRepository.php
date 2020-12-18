<?php

declare(strict_types=1);

namespace Storage\Doctrine\Main\Repository;

use Storage\Doctrine\Main\Entity\Category;

class CategoryRepository extends BaseRepository
{
    public const ENTITY_CLASS_NAME = Category::class;

    protected function getEntityClassName(): string
    {
        return self::ENTITY_CLASS_NAME;
    }
}
