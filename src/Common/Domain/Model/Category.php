<?php

declare(strict_types=1);

namespace Common\Domain\Model;

class Category
{
    private string $id;
    private string $name;

    /** @var Article[] */
    private array $articles;

    /**
     * @param Article[] $articles
     */
    public function __construct(string $id, string $name, array $articles)
    {
        $this->id = $id;
        $this->name = $name;
        $this->articles = $articles;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return Article[] */
    public function getArticles(): array
    {
        return $this->articles;
    }
}
