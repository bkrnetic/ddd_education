<?php

namespace Storage\Doctrine\Main\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use Serializable;
use Storage\Doctrine\Main\Entity\Traits\UuidPrimaryKeyEntityTrait;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User implements UserInterface, Serializable, StorageEntity
{
    use UuidPrimaryKeyEntityTrait;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /** @var ?string */
    private $plainPassword;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string[]
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @var Collection<int, Category>
     * @ORM\OneToMany(targetEntity="Category", mappedBy="createdBy")
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $categories;

    public function __construct(?string $uuid = null)
    {
        if (empty($uuid)) {
            $uuid = Uuid::uuid4()->toString();
        }

        $this->id = $uuid;
        $this->isActive = true;
        $this->categories = new ArrayCollection();
    }

    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->name,
            $this->roles,
            $this->isActive,
        ]);
    }

    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->name,
            $this->roles,
            $this->isActive
        ] = unserialize($serialized);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Collection<int, Category> $categories
     */
    public function setCategories(Collection $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function addCategory(Category $category): void
    {
        if (!$this->getCategories()->contains($category)) {
            $this->getCategories()->add($category);
            $category->setCreatedBy($this);
        }
    }

    public function removeCategory(Category $category): void
    {
        if ($this->getCategories()->contains($category)) {
            $this->getCategories()->removeElement($category);
            if ($category->getCreatedBy() === $this) {
                $category->setCreatedBy(null);
            }
        }
    }
}
