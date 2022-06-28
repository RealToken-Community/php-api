<?php

namespace App\Entity;

use App\Repository\TokenlistNetworkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TokenlistNetworkRepository::class)
 */
class TokenlistNetwork
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $chainId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=TokenlistToken::class, mappedBy="chain", orphanRemoval=true)
     */
    private $tokenlistTokens;

    public function __construct()
    {
        $this->tokenlistTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChainId(): ?int
    {
        return $this->chainId;
    }

    public function setChainId(int $chainId): self
    {
        $this->chainId = $chainId;

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

    /**
     * @return Collection|TokenlistToken[]
     */
    public function getTokenlistTokens(): Collection|\Doctrine\Common\Collections\Collection
    {
        return $this->tokenlistTokens;
    }

    public function addTokenlistToken(TokenlistToken $tokenlistToken): self
    {
        if (!$this->tokenlistTokens->contains($tokenlistToken)) {
            $this->tokenlistTokens[] = $tokenlistToken;
            $tokenlistToken->setChain($this);
        }

        return $this;
    }

    public function removeTokenlistToken(TokenlistToken $tokenlistToken): self
    {
        if ($this->tokenlistTokens->removeElement($tokenlistToken)) {
            // set the owning side to null (unless already changed)
            if ($tokenlistToken->getChain() === $this) {
                $tokenlistToken->setChain(null);
            }
        }

        return $this;
    }
}
