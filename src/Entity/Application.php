<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
  #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
  private ?int $id;

  #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "applications"), ORM\JoinColumn(nullable: false)]
  private ?User $user;

  #[ORM\OneToOne(targetEntity: Quota::class, cascade: ["persist", "remove"])]
  private ?Quota $quota;

  #[ORM\Column(type: "string", length: 50)]
  private ?string $name;

  #[ORM\Column(type: "string", unique: true, nullable: true)]
  private string $apiToken;

  #[ORM\Column(type: "string", length: 255, nullable: true)]
  private ?string $referer;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): self
  {
    $this->user = $user;

    return $this;
  }

  public function getQuota(): ?Quota
  {
    return $this->quota;
  }

  public function setQuota(?Quota $quota): self
  {
    $this->quota = $quota;

    return $this;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  public function getApiToken(): string
  {
    return $this->apiToken;
  }

  public function setApiToken(string $apiToken): void
  {
    $this->apiToken = $apiToken;
  }

  public function getReferer(): ?string
  {
    return $this->referer;
  }

  public function setReferer(?string $referer): self
  {
    $this->referer = $referer;

    return $this;
  }
}
