<?php

namespace App\Entity;

use App\Repository\TokenlistTagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TokenlistTagRepository::class)]
class TokenlistTag
{
  #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
  private ?int $id;

  #[ORM\Column(type: "string", length: 100)]
  private ?string $tagKey;

  #[ORM\Column(type: "string", length: 100)]
  private ?string $name;

  #[ORM\Column(type: "string", length: 150)]
  private ?string $description;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTagKey(): ?string
  {
    return $this->tagKey;
  }

  public function setTagKey(string $tagKey): self
  {
    $this->tagKey = $tagKey;

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

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(string $description): self
  {
    $this->description = $description;

    return $this;
  }
}
