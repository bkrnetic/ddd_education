<?php

declare(strict_types=1);

namespace UserIdentity\Application\Model;

final class User
{
    private string $id;
    private string $username;
    private string $email;
    private ?string $name;

    public function __construct(string $id, string $username, string $email, ?string $name)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
