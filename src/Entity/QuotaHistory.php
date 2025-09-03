<?php

namespace App\Entity;

use App\Repository\QuotaHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuotaHistoryRepository::class)]
class QuotaHistory
{
  #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
  private ?int $id;

  #[ORM\ManyToOne(targetEntity: Quota::class, inversedBy: "quotaHistories"), ORM\JoinColumn(nullable: false)]
  private ?Quota $quota;

  #[ORM\Column(type: "datetime")]
  private ?\DateTimeInterface $accessTime;

  public function getId(): ?int
  {
    return $this->id;
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

  public function getAccessTime(): ?\DateTimeInterface
  {
    return $this->accessTime;
  }

  public function setAccessTime(\DateTimeInterface $accessTime): self
  {
    $this->accessTime = $accessTime;

    return $this;
  }
}
