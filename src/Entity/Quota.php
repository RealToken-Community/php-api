<?php

namespace App\Entity;

use App\Repository\QuotaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuotaRepository::class)
 */
class Quota
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Application::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $application;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $increment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getIncrement(): ?int
    {
        return $this->increment;
    }

    public function setIncrement(int $increment = 1): self
    {
        $this->increment += $increment;

        return $this;
    }
}
