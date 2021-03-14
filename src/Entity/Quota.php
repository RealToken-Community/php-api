<?php

namespace App\Entity;

use App\Repository\QuotaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity=QuotaHistory::class, mappedBy="quota", orphanRemoval=true)
     */
    private $quotaHistories;

    public function __construct()
    {
        $this->quotaHistories = new ArrayCollection();
    }

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

    /**
     * @return Collection|QuotaHistory[]
     */
    public function getQuotaHistories(): Collection
    {
        return $this->quotaHistories;
    }

    public function addQuotaHistory(QuotaHistory $quotaHistory): self
    {
        if (!$this->quotaHistories->contains($quotaHistory)) {
            $this->quotaHistories[] = $quotaHistory;
            $quotaHistory->setQuota($this);
        }

        return $this;
    }

    public function removeQuotaHistory(QuotaHistory $quotaHistory): self
    {
        if ($this->quotaHistories->removeElement($quotaHistory)) {
            // set the owning side to null (unless already changed)
            if ($quotaHistory->getQuota() === $this) {
                $quotaHistory->setQuota(null);
            }
        }

        return $this;
    }
}
