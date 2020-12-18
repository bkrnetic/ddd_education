<?php

namespace Storage\Doctrine\Main\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait UuidPrimaryKeyEntityTrait
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="guid", name="id", nullable=false, unique=true)
     */
    protected $id;

    public function getUuid(): string
    {
        return (string) $this->id;
    }

    public function setUuid(string $uuid): void
    {
        $this->id = $uuid;
    }

    public function getId(): string
    {
        return (string) $this->getUuid();
    }
}
