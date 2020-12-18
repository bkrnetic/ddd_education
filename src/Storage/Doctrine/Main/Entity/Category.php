<?php

declare(strict_types=1);

namespace Storage\Doctrine\Main\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use Storage\Doctrine\Main\Entity\Traits\UuidPrimaryKeyEntityTrait;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="categories")
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class Category implements StorageEntity
{
    use UuidPrimaryKeyEntityTrait;
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var UserInterface|null
     * @ORM\ManyToOne(targetEntity="User", inversedBy="categories")
     * @ORM\JoinColumn(name="created_by_user_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $createdBy;

    /**
     * @var Collection<int, Article>
     * @ORM\OneToMany(
     *     targetEntity="Article",
     *     mappedBy="category",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist", "remove"},
     * orphanRemoval=true)
     */
    private $articles;

    public function __construct(?string $uuid = null)
    {
        if (null === $uuid) {
            $uuid = Uuid::uuid4()->toString();
        }

        $this->id = $uuid;
        $this->articles = new ArrayCollection();
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

    public function getCreatedBy(): ?UserInterface
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?UserInterface $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * @param Collection<int, Article> $articles
     */
    public function setArticles($articles): self
    {
        $this->articles = $articles;

        return $this;
    }

    public function addArticle(Article $article): void
    {
        if (!$this->getArticles()->contains($article)) {
            $this->getArticles()->add($article);
            $article->setCategory($this);
        }
    }

    public function removeArticle(Article $article): void
    {
        if ($this->getArticles()->contains($article)) {
            $this->getArticles()->removeElement($article);
            if ($article->getCategory() === $this) {
                $article->setCategory(null);
            }
        }
    }
}
