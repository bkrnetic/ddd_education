<?php

declare(strict_types=1);

namespace Common\Application\Formatter;

use Common\Domain\Model\Article;

class ArticleFormatter
{
    /**
     * @return array<string, mixed>
     */
    public function format(Article $article): array
    {
        return [
           'id' => $article->getId(),
           'name' => $article->getName(),
           'content' => $article->getContent(),
           'published' => $article->isPublished(),
       ];
    }
}
