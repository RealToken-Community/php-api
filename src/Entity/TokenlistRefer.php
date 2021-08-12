<?php

namespace App\Entity;

use App\Repository\TokenlistReferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TokenlistReferRepository::class)
 */
class TokenlistRefer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity=TokenlistIntegrity::class)
     */
    private $integrityTypes;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getIntegrityTypes(): ?TokenlistIntegrity
    {
        return $this->integrityTypes;
    }

    public function setIntegrityTypes(?TokenlistIntegrity $integrityTypes): self
    {
        $this->integrityTypes = $integrityTypes;

        return $this;
    }
}
