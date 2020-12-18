<?php

declare(strict_types=1);

namespace Common\Domain\Model;

class Article
{
    private string $id;
    private string $name;
    private string $content;
    private bool $published;

    public function __construct(string $id, string $name, string $content, bool $published)
    {
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
        $this->published = $published;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }
}
