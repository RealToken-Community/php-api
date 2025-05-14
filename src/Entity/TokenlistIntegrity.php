<?php

namespace App\Entity;

use App\Repository\TokenlistIntegrityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenlistIntegrityRepository::class)]
class TokenlistIntegrity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private ?int $id;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $timestamp;

    #[ORM\ManyToOne(targetEntity: TokenlistNetwork::class)]
    private ?TokenlistNetwork $network;

    #[ORM\Column(type: "integer")]
    private ?int $versionMajor;

    #[ORM\Column(type: "integer")]
    private ?int $versionMinor;

    #[ORM\Column(type: "integer")]
    private ?int $versionPatch;

    #[ORM\Column(type: "string", length: 32)]
    private ?string $hash;

    #[ORM\Column(type: "json")]
    private array $data = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getNetwork(): ?TokenlistNetwork
    {
        return $this->network;
    }

    public function setNetwork(?TokenlistNetwork $network): self
    {
        $this->network = $network;

        return $this;
    }

    public function getVersionMajor(): ?int
    {
        return $this->versionMajor;
    }

    public function setVersionMajor(int $versionMajor): self
    {
        $this->versionMajor = $versionMajor;

        return $this;
    }

    public function getVersionMinor(): ?int
    {
        return $this->versionMinor;
    }

    public function setVersionMinor(int $versionMinor): self
    {
        $this->versionMinor = $versionMinor;

        return $this;
    }

    public function getVersionPatch(): ?int
    {
        return $this->versionPatch;
    }

    public function setVersionPatch(int $versionPatch): self
    {
        $this->versionPatch = $versionPatch;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
