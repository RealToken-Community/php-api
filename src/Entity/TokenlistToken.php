<?php

namespace App\Entity;

use App\Repository\TokenlistTokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TokenlistTokenRepository::class)
 */
class TokenlistToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=42)
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity=TokenlistNetwork::class, inversedBy="tokenlistTokens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chain;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $symbol;

    /**
     * @ORM\Column(type="integer")
     */
    private $decimals;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $tags = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getChain(): ?TokenlistNetwork
    {
        return $this->chain;
    }

    public function setChain(?TokenlistNetwork $chain): self
    {
        $this->chain = $chain;

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

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getDecimals(): ?int
    {
        return $this->decimals;
    }

    public function setDecimals(int $decimals): self
    {
        $this->decimals = $decimals;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }
}
