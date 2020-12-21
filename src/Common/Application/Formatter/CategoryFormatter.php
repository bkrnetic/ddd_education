<?php

declare(strict_types=1);

namespace Common\Application\Formatter;

use Common\Domain\Model\Category;

class CategoryFormatter
{
    private ArticleFormatter $formatter;

    /**
     * CategoryFormatter constructor.
     */
    public function __construct(ArticleFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @return array<string, mixed>
     */
    public function format(Category $category): array
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'articles' => array_map([$this->formatter, 'format'], $category->getArticles())
        ];
    }
}
