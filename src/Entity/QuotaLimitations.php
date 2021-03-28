<?php

namespace App\Entity;

use App\Repository\QuotaLimitationsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuotaLimitationsRepository::class)
 */
class QuotaLimitations
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $role;

    /**
     * @ORM\Column(type="integer")
     */
    private $limitPerMinute;

    /**
     * @ORM\Column(type="integer")
     */
    private $limitPerHour;

    /**
     * @ORM\Column(type="integer")
     */
    private $limitPerDay;

    /**
     * @ORM\Column(type="integer")
     */
    private $limitPerWeek;

    /**
     * @ORM\Column(type="integer")
     */
    private $limitPerMonth;

    /**
     * @ORM\Column(type="integer")
     */
    private $limitPerYear;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getLimitPerMinute(): ?int
    {
        return $this->limitPerMinute;
    }

    public function setLimitPerMinute(int $limitPerMinute): self
    {
        $this->limitPerMinute = $limitPerMinute;

        return $this;
    }

    public function getLimitPerHour(): ?int
    {
        return $this->limitPerHour;
    }

    public function setLimitPerHour(int $limitPerHour): self
    {
        $this->limitPerHour = $limitPerHour;

        return $this;
    }

    public function getLimitPerDay(): ?int
    {
        return $this->limitPerDay;
    }

    public function setLimitPerDay(int $limitPerDay): self
    {
        $this->limitPerDay = $limitPerDay;

        return $this;
    }

    public function getLimitPerWeek(): ?int
    {
        return $this->limitPerWeek;
    }

    public function setLimitPerWeek(int $limitPerWeek): self
    {
        $this->limitPerWeek = $limitPerWeek;

        return $this;
    }

    public function getLimitPerMonth(): ?int
    {
        return $this->limitPerMonth;
    }

    public function setLimitPerMonth(int $limitPerMonth): self
    {
        $this->limitPerMonth = $limitPerMonth;

        return $this;
    }

    public function getLimitPerYear(): ?int
    {
        return $this->limitPerYear;
    }

    public function setLimitPerYear(int $limitPerYear): self
    {
        $this->limitPerYear = $limitPerYear;

        return $this;
    }

    public function __toArray(): array
    {
        return [
            'limitPerMinute' => $this->limitPerMinute,
            'limitPerHour' => $this->limitPerHour,
            'limitPerDay' => $this->limitPerDay,
            'limitPerWeek' => $this->limitPerWeek,
            'limitPerMonth' => $this->limitPerMonth,
            'limitPerYear' => $this->limitPerYear,
        ];
    }
}
