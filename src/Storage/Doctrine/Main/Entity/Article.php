<?php

declare(strict_types=1);

namespace Storage\Doctrine\Main\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use Storage\Doctrine\Main\Entity\Traits\UuidPrimaryKeyEntityTrait;

/**
 * @ORM\Table(name="articles")
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class Article implements StorageEntity
{
    use UuidPrimaryKeyEntityTrait;
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $published;

    /**
     * @var ?Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="articles")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $category;

    public function __construct(?string $uuid = null)
    {
        if (null === $uuid) {
            $uuid = Uuid::uuid4()->toString();
        }

        $this->id = $uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
