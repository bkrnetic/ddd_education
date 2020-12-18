<?php

declare(strict_types=1);

namespace Common\Application\Formatter;

use Common\Domain\Model\Category;

class CategoryFormatter
{
    /**
     * @return array<string, mixed>
     */
    public function format(Category $category): array
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
        ];
    }
}
