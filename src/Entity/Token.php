<?php

namespace App\Entity;

use App\Repository\TokenRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TokenRepository::class)
 * @ORM\Table(name="tokens")
 */
class Token
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $shortName;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $tokenPrice;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $publicSale;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $canal;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $currency;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalTokens;

    /**
     * @ORM\Column(type="string", length=42, unique=true)
     */
    private $ethereumContract;

    /**
     * @ORM\Column(type="string", length=42, nullable=true)
     */
    private $ethereumDistributor;

    /**
     * @ORM\Column(type="string", length=42, nullable=true)
     */
    private $ethereumMaintenance;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $assetPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $grossRentMonth;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $annualPercentageYield;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $propertyManagementPercent;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $realtPlatformPercent;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $insurance;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $propertyTaxes;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $utilities;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $propertyMaintenance;

    /**
     * @var array $coordinate
     * @ORM\Column(type="array", nullable=true)
     */
    private $coordinate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $marketplaceLink;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $imageLink = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $propertyType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $squareFeet;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lotSize;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $bedroomBath;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasTenants;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $termOfLease;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $renewalDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $section8paid;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $sellPropertyTo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $onUniswap;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $grossRentYear;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $propertyManagement;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $realtPlatform;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $netRentMonth;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $netRentYear;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $netRentYearPerToken;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $netRentMonthPerToken;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastUpdate;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $netRentDay;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $netRentDayPerToken;

//    /**
//     * Token constructor.
//     * @param $id
//     * @param $fullName
//     * @param $shortName
//     * @param $tokenPrice
//     * @param $publicSale
//     * @param $canal
//     * @param $currency
//     * @param $totalTokens
//     * @param $ethereumContract
//     * @param $ethereumDistributor
//     * @param $ethereumMaintenance
//     * @param $assetPrice
//     * @param $grossRentMonth
//     * @param $annualPercentageYield
//     * @param $propertyManagementPercent
//     * @param $realtPlatformPercent
//     * @param $insurance
//     * @param $propertyTaxes
//     * @param $utilities
//     * @param $propertyMaintenance
//     * @param array $coordinate
//     * @param $marketplaceLink
//     * @param array $imageLink
//     * @param $propertyType
//     * @param $squareFeet
//     * @param $lotSize
//     * @param $bedroomBath
//     * @param $hasTenants
//     * @param $termOfLease
//     * @param $renewalDate
//     * @param $section8paid
//     * @param $sellPropertyTo
//     * @param $onUniswap
//     * @param $grossRentYear
//     * @param $propertyManagement
//     * @param $realtPlatform
//     * @param $netRentMonth
//     * @param $netRentYear
//     * @param $netRentYearPerToken
//     * @param $netRentMonthPerToken
//     * @param $lastUpdate
//     * @param $netRentDay
//     * @param $netRentDayPerToken
//     */
//    public function __construct($id, $fullName, $shortName, $tokenPrice, $publicSale, $canal, $currency, $totalTokens, $ethereumContract, $ethereumDistributor, $ethereumMaintenance, $assetPrice, $grossRentMonth, $annualPercentageYield, $propertyManagementPercent, $realtPlatformPercent, $insurance, $propertyTaxes, $utilities, $propertyMaintenance, array $coordinate, $marketplaceLink, array $imageLink, $propertyType, $squareFeet, $lotSize, $bedroomBath, $hasTenants, $termOfLease, $renewalDate, $section8paid, $sellPropertyTo, $onUniswap, $grossRentYear, $propertyManagement, $realtPlatform, $netRentMonth, $netRentYear, $netRentYearPerToken, $netRentMonthPerToken, $lastUpdate, $netRentDay, $netRentDayPerToken)
//    {
//        $this->id = $id;
//        $this->fullName = $fullName;
//        $this->shortName = $shortName;
//        $this->tokenPrice = $tokenPrice;
//        $this->publicSale = $publicSale;
//        $this->canal = $canal;
//        $this->currency = $currency;
//        $this->totalTokens = $totalTokens;
//        $this->ethereumContract = $ethereumContract;
//        $this->ethereumDistributor = $ethereumDistributor;
//        $this->ethereumMaintenance = $ethereumMaintenance;
//        $this->assetPrice = $assetPrice;
//        $this->grossRentMonth = $grossRentMonth;
//        $this->annualPercentageYield = $annualPercentageYield;
//        $this->propertyManagementPercent = $propertyManagementPercent;
//        $this->realtPlatformPercent = $realtPlatformPercent;
//        $this->insurance = $insurance;
//        $this->propertyTaxes = $propertyTaxes;
//        $this->utilities = $utilities;
//        $this->propertyMaintenance = $propertyMaintenance;
//        $this->coordinate = $coordinate;
//        $this->marketplaceLink = $marketplaceLink;
//        $this->imageLink = $imageLink;
//        $this->propertyType = $propertyType;
//        $this->squareFeet = $squareFeet;
//        $this->lotSize = $lotSize;
//        $this->bedroomBath = $bedroomBath;
//        $this->hasTenants = $hasTenants;
//        $this->termOfLease = $termOfLease;
//        $this->renewalDate = $renewalDate;
//        $this->section8paid = $section8paid;
//        $this->sellPropertyTo = $sellPropertyTo;
//        $this->onUniswap = $onUniswap;
//        $this->grossRentYear = $grossRentYear;
//        $this->propertyManagement = $propertyManagement;
//        $this->realtPlatform = $realtPlatform;
//        $this->netRentMonth = $netRentMonth;
//        $this->netRentYear = $netRentYear;
//        $this->netRentYearPerToken = $netRentYearPerToken;
//        $this->netRentMonthPerToken = $netRentMonthPerToken;
//        $this->lastUpdate = $lastUpdate;
//        $this->netRentDay = $netRentDay;
//        $this->netRentDayPerToken = $netRentDayPerToken;
//    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): self
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getTokenPrice(): ?float
    {
        return $this->tokenPrice;
    }

    public function setTokenPrice(?float $tokenPrice): self
    {
        $this->tokenPrice = $tokenPrice;

        return $this;
    }

    public function getPublicSale(): ?string
    {
        return $this->publicSale;
    }

    public function setPublicSale(?string $publicSale): self
    {
        $this->publicSale = $publicSale;

        return $this;
    }

    public function getCanal(): ?string
    {
        return $this->canal;
    }

    public function setCanal(string $canal): self
    {
        $this->canal = $canal;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getTotalTokens(): ?int
    {
        return $this->totalTokens;
    }

    public function setTotalTokens(int $totalTokens): self
    {
        $this->totalTokens = $totalTokens;

        return $this;
    }

    public function getEthereumContract(): ?string
    {
        return $this->ethereumContract;
    }

    public function setEthereumContract(string $ethereumContract): self
    {
        $this->ethereumContract = $ethereumContract;

        return $this;
    }

    public function getEthereumDistributor(): ?string
    {
        return $this->ethereumDistributor;
    }

    public function setEthereumDistributor(?string $ethereumDistributor): self
    {
        $this->ethereumDistributor = $ethereumDistributor;

        return $this;
    }

    public function getEthereumMaintenance(): ?string
    {
        return $this->ethereumMaintenance;
    }

    public function setEthereumMaintenance(?string $ethereumMaintenance): self
    {
        $this->ethereumMaintenance = $ethereumMaintenance;

        return $this;
    }

    public function getAssetPrice(): ?float
    {
        return $this->assetPrice;
    }

    public function setAssetPrice(?float $assetPrice): self
    {
        $this->assetPrice = $assetPrice;

        return $this;
    }

    public function getGrossRentMonth(): ?float
    {
        return $this->grossRentMonth;
    }

    public function setGrossRentMonth(?float $grossRentMonth): self
    {
        $this->grossRentMonth = $grossRentMonth;

        return $this;
    }

    public function getGrossRentYear(): ?float
    {
        return $this->grossRentYear;
    }

    public function setGrossRentYear(?float $grossRentYear): self
    {
        $this->grossRentYear = $grossRentYear;

        return $this;
    }

    public function getPropertyManagement(): ?float
    {
        return $this->propertyManagement;
    }

    public function setPropertyManagement(?float $propertyManagement): self
    {
        $this->propertyManagement = $propertyManagement;

        return $this;
    }

    public function getPropertyManagementPercent(): ?float
    {
        return $this->propertyManagementPercent;
    }

    public function setPropertyManagementPercent(?float $propertyManagementPercent): self
    {
        $this->propertyManagementPercent = $propertyManagementPercent;

        return $this;
    }

    public function getRealtPlatform(): ?float
    {
        return $this->realtPlatform;
    }

    public function setRealtPlatform(?float $realtPlatform): self
    {
        $this->realtPlatform = $realtPlatform;

        return $this;
    }

    public function getRealtPlatformPercent(): ?float
    {
        return $this->realtPlatformPercent;
    }

    public function setRealtPlatformPercent(float $realtPlatformPercent): self
    {
        $this->realtPlatformPercent = $realtPlatformPercent;

        return $this;
    }

    public function getInsurance(): ?float
    {
        return $this->insurance;
    }

    public function setInsurance(?float $insurance): self
    {
        $this->insurance = $insurance;

        return $this;
    }

    public function getPropertyTaxes(): ?float
    {
        return $this->propertyTaxes;
    }

    public function setPropertyTaxes(?float $propertyTaxes): self
    {
        $this->propertyTaxes = $propertyTaxes;

        return $this;
    }

    public function getUtilities(): ?string
    {
        return $this->utilities;
    }

    public function setUtilities(?string $utilities): self
    {
        $this->utilities = $utilities;

        return $this;
    }

    public function getPropertyMaintenance(): ?float
    {
        return $this->propertyMaintenance;
    }

    public function setPropertyMaintenance(?float $propertyMaintenance): self
    {
        $this->propertyMaintenance = $propertyMaintenance;

        return $this;
    }

    public function getNetRentDay(): ?float
    {
        return $this->netRentDay;
    }

    public function setNetRentDay(?float $netRentDay): self
    {
        $this->netRentDay = $netRentDay;

        return $this;
    }

    public function getNetRentMonth(): ?float
    {
        return $this->netRentMonth;
    }

    public function setNetRentMonth(?float $netRentMonth): self
    {
        $this->netRentMonth = $netRentMonth;

        return $this;
    }

    public function getNetRentYear(): ?float
    {
        return $this->netRentYear;
    }

    public function setNetRentYear(?float $netRentYear): self
    {
        $this->netRentYear = $netRentYear;

        return $this;
    }

    public function getNetRentYearPerToken(): ?float
    {
        return $this->netRentYearPerToken;
    }

    public function setNetRentYearPerToken(?float $netRentYearPerToken): self
    {
        $this->netRentYearPerToken = $netRentYearPerToken;

        return $this;
    }

    public function getNetRentMonthPerToken(): ?float
    {
        return $this->netRentMonthPerToken;
    }

    public function setNetRentMonthPerToken(?float $netRentMonthPerToken): self
    {
        $this->netRentMonthPerToken = $netRentMonthPerToken;

        return $this;
    }

    public function getNetRentDayPerToken(): ?float
    {
        return $this->netRentDayPerToken;
    }

    public function setNetRentDayPerToken(?float $netRentDayPerToken): self
    {
        $this->netRentDayPerToken = $netRentDayPerToken;

        return $this;
    }

    public function getAnnualPercentageYield(): ?float
    {
        return $this->annualPercentageYield;
    }

    public function setAnnualPercentageYield(?float $annualPercentageYield): self
    {
        $this->annualPercentageYield = $annualPercentageYield;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getMarketplaceLink(): ?string
    {
        return $this->marketplaceLink;
    }

    public function setMarketplaceLink(?string $marketplaceLink): self
    {
        $this->marketplaceLink = $marketplaceLink;

        return $this;
    }

    public function getImageLink(): ?array
    {
        return $this->imageLink;
    }

    public function setImageLink(?array $imageLink): self
    {
        $this->imageLink = $imageLink;

        return $this;
    }

    public function getPropertyType(): ?int
    {
        return $this->propertyType;
    }

    public function setPropertyType(?int $propertyType): self
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    public function getSquareFeet(): ?int
    {
        return $this->squareFeet;
    }

    public function setSquareFeet(?int $squareFeet): self
    {
        $this->squareFeet = $squareFeet;

        return $this;
    }

    public function getLotSize(): ?int
    {
        return $this->lotSize;
    }

    public function setLotSize(?int $lotSize): self
    {
        $this->lotSize = $lotSize;

        return $this;
    }

    public function getBedroomBath(): ?string
    {
        return $this->bedroomBath;
    }

    public function setBedroomBath(?string $bedroomBath): self
    {
        $this->bedroomBath = $bedroomBath;

        return $this;
    }

    public function getHasTenants(): ?bool
    {
        return $this->hasTenants;
    }

    public function setHasTenants(?bool $hasTenants): self
    {
        $this->hasTenants = $hasTenants;

        return $this;
    }

    public function getTermOfLease(): ?string
    {
        return $this->termOfLease;
    }

    public function setTermOfLease(?string $termOfLease): self
    {
        $this->termOfLease = $termOfLease;

        return $this;
    }

    public function getRenewalDate(): ?\DateTime
    {
        return $this->renewalDate;
    }

    public function setRenewalDate(?\DateTime $renewalDate): self
    {
        $this->renewalDate = $renewalDate;

        return $this;
    }

    public function getSection8paid(): ?int
    {
        return $this->section8paid;
    }

    public function setSection8paid(?int $section8paid): self
    {
        $this->section8paid = $section8paid;

        return $this;
    }

    public function getSellPropertyTo(): ?string
    {
        return $this->sellPropertyTo;
    }

    public function setSellPropertyTo(?string $sellPropertyTo): self
    {
        $this->sellPropertyTo = $sellPropertyTo;

        return $this;
    }

    public function getOnUniswap(): ?bool
    {
        return $this->onUniswap;
    }

    public function setOnUniswap(?bool $onUniswap): self
    {
        $this->onUniswap = $onUniswap;

        return $this;
    }

    /**
     * @return array
     */
    public function getCoordinate(): array
    {
        return $this->coordinate;
    }

    /**
     * @param array $coordinate
     */
    public function setCoordinate(array $coordinate): void
    {
        $this->coordinate = $coordinate;
    }

    public function getLastUpdate(): ?DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(DateTimeInterface $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function __toArray($isAuth = false): array
    {
        if ($isAuth) {
            $response = [
                'fullName' => $this->fullName,
                'shortName' => $this->shortName,
                'tokenPrice' => $this->tokenPrice,
                'publicSale' => $this->publicSale,
                'canal' => $this->canal,
                'currency' => $this->currency,
                'totalTokens' => $this->totalTokens,
                'ethereumContract' => $this->ethereumContract,
                'ethereumDistributor' => $this->ethereumDistributor,
                'ethereumMaintenance' => $this->ethereumMaintenance,
                'assetPrice' => $this->assetPrice,
                'grossRentYear' => $this->grossRentYear,
                'grossRentMonth' => $this->grossRentMonth,
                'propertyManagement' => $this->propertyManagement,
                'propertyManagementPercent' => $this->propertyManagementPercent,
                'realtPlatform' => $this->realtPlatform,
                'realtPlatformPercent' => $this->realtPlatformPercent,
                'insurance' => $this->insurance,
                'propertyTaxes' => $this->propertyTaxes,
                'utilities' => $this->utilities,
                'propertyMaintenance' => $this->propertyMaintenance,
                'netRentDay' => $this->netRentDay,
                'netRentMonth' => $this->netRentMonth,
                'netRentYear' => $this->netRentYear,
                'netRentDayPerToken' => $this->netRentDayPerToken,
                'netRentMonthPerToken' => $this->netRentMonthPerToken,
                'netRentYearPerToken' => $this->netRentYearPerToken,
                'annualPercentageYield' => $this->annualPercentageYield,
                'coordinate' => $this->coordinate,
                'marketplaceLink' => $this->marketplaceLink,
                'imageLink' => $this->imageLink,
                'propertyType' => $this->propertyType,
                'squareFeet' => $this->squareFeet,
                'lotSize' => $this->lotSize,
                'bedroomBath' => $this->bedroomBath,
                'hasTenants' => $this->hasTenants,
                'termOfLease' => $this->termOfLease,
                'renewalDate' => $this->renewalDate,
                'section8paid' => $this->section8paid,
                'sellPropertyTo' => $this->sellPropertyTo,
                'onUniswap' => $this->onUniswap,
                'lastUpdate' => $this->lastUpdate
            ];
        } else {
            $response = [
                'fullName' => $this->fullName,
                'shortName' => $this->shortName,
                'tokenPrice' => $this->tokenPrice,
                'currency' => $this->currency,
                'ethereumContract' => $this->ethereumContract,
                'lastUpdate' => $this->lastUpdate
            ];
        }
        return $response;
    }
}