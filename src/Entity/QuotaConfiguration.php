<?php

namespace App\Entity;

use App\Repository\QuotaConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuotaConfigurationRepository::class)]
class QuotaConfiguration
{
  #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
  private ?int $id;

  #[ORM\Column(type: "string", length: 50)]
  private ?string $name;

  #[ORM\Column(type: "integer")]
  private ?int $limitation;

  #[ORM\Column(type: "integer")]
  private ?int $intervalNumber;

  #[ORM\Column(type: "string", length: 50)]
  private ?string $intervalType;

  public function getId(): ?int
  {
    return $this->id;
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

  public function getLimitation(): ?int
  {
    return $this->limitation;
  }

  public function setLimitation(int $limitation): self
  {
    $this->limitation = $limitation;

    return $this;
  }

  public function getIntervalNumber(): ?int
  {
    return $this->intervalNumber;
  }

  public function setIntervalNumber(int $intervalNumber): self
  {
    $this->intervalNumber = $intervalNumber;

    return $this;
  }

  public function getIntervalType(): ?string
  {
    return $this->intervalType;
  }

  public function setIntervalType(string $intervalType): self
  {
    $this->intervalType = $intervalType;

    return $this;
  }
}
