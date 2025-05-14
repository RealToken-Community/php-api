<?php

namespace App\Entity;

use AllowDynamicProperties;
use App\Repository\TokenRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Table(name: 'tokens'), ORM\Entity(repositoryClass: TokenRepository::class)]
#[AllowDynamicProperties] class Token
{
    const CANAL_RELEASE = "release";
    const CANAL_COMING_SOON = "coming_soon";
    const CANAL_OFFERING_CLOSED = "offering_closed";
    const CANAL_OFFERING_CANCELED = "offering_canceled";
    const CANAL_EXIT_PROPOSED = "exit_proposed";
    const CANAL_EXIT_COMPLETE = "exit_complete";
    const CANAL_MIGRATED = "tokens_migrated";

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[OA\Property(type: 'string', maxLength: 100)]
    private ?string $fullName;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $shortName;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $tokenPrice;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $canal;

    #[ORM\Column(type: 'string', length: 3, nullable: true)]
    private ?string $currency;

    #[ORM\Column(type: 'integer')]
    private ?int $totalTokens;

    #[ORM\Column(type: 'string', length: 42, nullable: true)]
    private ?string $ethereumContract;

    #[ORM\Column(type: 'string', length: 42, nullable: true)]
    private ?string $xDaiContract;

    #[ORM\Column(type: 'string', length: 42, nullable: true)]
    private ?string $gnosisContract;

    #[ORM\Column(type: 'string', length: 42, nullable: true)]
    private ?string $goerliContract;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $totalInvestment;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $grossRentMonth;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $annualPercentageYield;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $propertyManagementPercent;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $realtPlatformPercent;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $insurance;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $propertyTaxes;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $utilities;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $initialMaintenanceReserve;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $coordinate;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $marketplaceLink;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $imageLink = [];

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $propertyType;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $squareFeet;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $lotSize;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $bedroomBath;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $hasTenants;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $termOfLease;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $renewalDate;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $section8paid;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $sellPropertyTo;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $grossRentYear;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $propertyManagement;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $realtPlatform;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $netRentMonth;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $netRentYear;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $netRentYearPerToken;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $netRentMonthPerToken;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $lastUpdate;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $netRentDay;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $netRentDayPerToken;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $rentedUnits;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $totalUnits;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $secondaryMarketplace = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private array $secondaryMarketplaces = [];

     #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $symbol;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $blockchainAddresses = [];

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $underlyingAssetPrice;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $renovationReserve;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $propertyMaintenanceMonthly;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $rentStartDate;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $originSecondaryMarketplaces = [];

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $initialLaunchDate;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $seriesNumber;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $constructionYear;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $constructionType;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $roofType;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $assetParking;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $foundation;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $heating;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $cooling;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $tokenIdRules;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $rentCalculationType;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $realtListingFeePercent;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $realtListingFee;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $miscellaneousCosts;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $propertyStories;

    #[ORM\Column(type: 'string', length: 42, unique: true)]
    private ?string $uuid;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $totalTokensRegSummed;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $rentalType;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $subsidyStatus;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $subsidyStatusValue;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $subsidyBy;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private string $productType;

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

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getEthereumContract(): ?string
    {
        return $this->ethereumContract;
    }

    public function setEthereumContract(?string $ethereumContract): self
    {
        $this->ethereumContract = $ethereumContract;

        return $this;
    }

    public function getXDaiContract(): ?string
    {
        return $this->xDaiContract;
    }

    public function setXDaiContract(?string $xDaiContract): self
    {
        $this->xDaiContract = $xDaiContract;

        return $this;
    }

    public function getGnosisContract(): ?string
    {
        return $this->gnosisContract;
    }

    public function setGnosisContract(?string $gnosisContract): self
    {
        $this->gnosisContract = $gnosisContract;

        return $this;
    }

    public function getGoerliContract(): ?string
    {
        return $this->goerliContract;
    }

    public function setGoerliContract(?string $goerliContract): self
    {
        $this->goerliContract = $goerliContract;

        return $this;
    }

    public function getTotalInvestment(): ?float
    {
        return $this->totalInvestment;
    }

    public function setTotalInvestment(?float $totalInvestment): self
    {
        $this->totalInvestment = $totalInvestment;

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

    public function getUtilities(): ?float
    {
        return $this->utilities;
    }

    public function setUtilities(?float $utilities): self
    {
        $this->utilities = $utilities;

        return $this;
    }

    public function getInitialMaintenanceReserve(): ?float
    {
        return $this->initialMaintenanceReserve;
    }

    public function setInitialMaintenanceReserve(?float $initialMaintenanceReserve): self
    {
        $this->initialMaintenanceReserve = $initialMaintenanceReserve;

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

    public function getCoordinate(): array
    {
        return $this->coordinate;
    }

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

    public function getRentedUnits(): ?int
    {
        return $this->rentedUnits;
    }

    public function setRentedUnits(?int $rentedUnits): self
    {
        $this->rentedUnits = $rentedUnits;

        return $this;
    }

    public function getTotalUnits(): ?int
    {
        return $this->totalUnits;
    }

    public function setTotalUnits(?int $totalUnits): self
    {
        $this->totalUnits = $totalUnits;

        return $this;
    }

    public function getSecondaryMarketplace(): ?array
    {
        return $this->secondaryMarketplace;
    }

    public function setSecondaryMarketplace(?array $secondaryMarketplace): self
    {
        $this->secondaryMarketplace = $secondaryMarketplace;

        return $this;
    }

    public function getSecondaryMarketplaces(): ?array
    {
        return $this->secondaryMarketplaces ?: [];
    }

    public function setSecondaryMarketplaces(?array $secondaryMarketplaces): self
    {
        $this->secondaryMarketplaces = $secondaryMarketplaces;

        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(?string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getBlockchainAddresses(): ?array
    {
        return $this->blockchainAddresses;
    }

    public function setBlockchainAddresses(?array $blockchainAddresses): self
    {
        $this->blockchainAddresses = $blockchainAddresses;

        return $this;
    }

    public function getUnderlyingAssetPrice(): ?float
    {
        return $this->underlyingAssetPrice;
    }

    public function setUnderlyingAssetPrice(?float $underlyingAssetPrice): self
    {
        $this->underlyingAssetPrice = $underlyingAssetPrice;

        return $this;
    }

    public function getRenovationReserve(): ?float
    {
        return $this->renovationReserve;
    }

    public function setRenovationReserve(?float $renovationReserve): self
    {
        $this->renovationReserve = $renovationReserve;

        return $this;
    }

    public function getPropertyMaintenanceMonthly(): ?float
    {
        return $this->propertyMaintenanceMonthly;
    }

    public function setPropertyMaintenanceMonthly(?float $propertyMaintenanceMonthly): self
    {
        $this->propertyMaintenanceMonthly = $propertyMaintenanceMonthly;

        return $this;
    }

    public function getRentStartDate(): ?\DateTimeInterface
    {
        return $this->rentStartDate;
    }

    public function setRentStartDate(?\DateTimeInterface $rentStartDate): self
    {
        $this->rentStartDate = $rentStartDate;

        return $this;
    }

    public function getOriginSecondaryMarketplaces(): ?array
    {
        return $this->originSecondaryMarketplaces;
    }

    public function setOriginSecondaryMarketplaces(?array $originSecondaryMarketplaces): self
    {
        $this->originSecondaryMarketplaces = $originSecondaryMarketplaces;

        return $this;
    }

    public function getInitialLaunchDate(): ?\DateTimeInterface
    {
        return $this->initialLaunchDate;
    }

    public function setInitialLaunchDate(?\DateTimeInterface $initialLaunchDate): self
    {
        $this->initialLaunchDate = $initialLaunchDate;

        return $this;
    }

    public function getSeriesNumber(): ?int
    {
        return $this->seriesNumber;
    }

    public function setSeriesNumber(?int $seriesNumber): self
    {
        $this->seriesNumber = $seriesNumber;

        return $this;
    }

    public function getConstructionYear(): ?int
    {
        return $this->constructionYear;
    }

    public function setConstructionYear(?int $constructionYear): self
    {
        $this->constructionYear = $constructionYear;

        return $this;
    }

    public function getConstructionType(): ?string
    {
        return $this->constructionType;
    }

    public function setConstructionType(?string $constructionType): self
    {
        $this->constructionType = $constructionType;

        return $this;
    }

    public function getRoofType(): ?string
    {
        return $this->roofType;
    }

    public function setRoofType(?string $roofType): self
    {
        $this->roofType = $roofType;

        return $this;
    }

    public function getAssetParking(): ?string
    {
        return $this->assetParking;
    }

    public function setAssetParking(?string $assetParking): self
    {
        $this->assetParking = $assetParking;

        return $this;
    }

    public function getFoundation(): ?string
    {
        return $this->foundation;
    }

    public function setFoundation(?string $foundation): self
    {
        $this->foundation = $foundation;

        return $this;
    }

    public function getHeating(): ?string
    {
        return $this->heating;
    }

    public function setHeating(?string $heating): self
    {
        $this->heating = $heating;

        return $this;
    }

    public function getCooling(): ?string
    {
        return $this->cooling;
    }

    public function setCooling(?string $cooling): self
    {
        $this->cooling = $cooling;

        return $this;
    }

    public function getTokenIdRules(): ?int
    {
        return $this->tokenIdRules;
    }

    public function setTokenIdRules(?int $tokenIdRules): self
    {
        $this->tokenIdRules = $tokenIdRules;

        return $this;
    }

    public function getRentCalculationType(): ?string
    {
        return $this->rentCalculationType;
    }

    public function setRentCalculationType(?string $rentCalculationType): self
    {
        $this->rentCalculationType = $rentCalculationType;

        return $this;
    }

    public function getRealtListingFeePercent(): ?float
    {
        return $this->realtListingFeePercent;
    }

    public function setRealtListingFeePercent(?float $realtListingFeePercent): self
    {
        $this->realtListingFeePercent = $realtListingFeePercent;

        return $this;
    }

    public function getRealtListingFee(): ?float
    {
        return $this->realtListingFee;
    }

    public function setRealtListingFee(?float $realtListingFee): self
    {
        $this->realtListingFee = $realtListingFee;

        return $this;
    }

    public function getMiscellaneousCosts(): ?float
    {
        return $this->miscellaneousCosts;
    }

    public function setMiscellaneousCosts(?float $miscellaneousCosts): self
    {
        $this->miscellaneousCosts = $miscellaneousCosts;

        return $this;
    }

    public function getPropertyStories(): ?int
    {
        return $this->propertyStories;
    }

    public function setPropertyStories(?int $propertyStories): self
    {
        $this->propertyStories = $propertyStories;

        return $this;
    }

    public function getTotalTokensRegSummed(): ?int
    {
        return $this->totalTokensRegSummed;
    }

    public function setTotalTokensRegSummed(?int $totalTokensRegSummed): self
    {
        $this->totalTokensRegSummed = $totalTokensRegSummed;

        return $this;
    }

    public function getRentalType(): ?string
    {
      return $this->rentalType;
    }

    public function setRentalType(?string $rentalType): self
    {
      $this->rentalType = $rentalType;

      return $this;
    }

    public function getSubsidyStatus(): ?string
    {
      return $this->subsidyStatus;
    }

    public function setSubsidyStatus(?string $subsidyStatus): self
    {
      $this->subsidyStatus = $subsidyStatus;

      return $this;
    }

    public function getSubsidyStatusValue(): ?float
    {
      return $this->subsidyStatusValue;
    }

    public function setSubsidyStatusValue(?float $subsidyStatusValue): self
    {
      $this->subsidyStatusValue = $subsidyStatusValue;

      return $this;
    }

    public function getSubsidyBy(): ?string
    {
      return $this->subsidyBy;
    }

    public function setSubsidyBy(?string $subsidyBy): self
    {
      $this->subsidyBy = $subsidyBy;

      return $this;
    }

    public function getProductType(): string
    {
      return $this->productType;
    }

    public function setProductType(string $productType): self
    {
      $this->productType = $productType;

      return $this;
    }

    public function __toArray(array $credentials): array
    {
        // Check if canal is available for public & check rights
        if (!in_array($this->getCanal(), [
          Token::CANAL_RELEASE,
          Token::CANAL_OFFERING_CLOSED,
          Token::CANAL_EXIT_PROPOSED,
          Token::CANAL_EXIT_COMPLETE,
          Token::CANAL_MIGRATED
        ])) {
            if (!$credentials['isAdmin']) {
                return [];
            }
        }

        if ($credentials['isAuth']) {
            $response = [
                'fullName' => $this->fullName,
                'shortName' => $this->shortName,
                'symbol' => $this->symbol,
                'productType' => $this->productType,
                'tokenPrice' => $this->tokenPrice,
                'canal' => $this->canal,
                'currency' => $this->currency,
                'totalTokens' => $this->totalTokens,
                'totalTokensRegSummed' => $this->totalTokensRegSummed,
                'uuid' => $this->uuid,
                'ethereumContract' => $this->ethereumContract,
                'xDaiContract' => $this->xDaiContract,
                'gnosisContract' => $this->gnosisContract,
                'goerliContract' => $this->goerliContract,
                'totalInvestment' => $this->totalInvestment,
                'grossRentYear' => $this->grossRentYear,
                'grossRentMonth' => $this->grossRentMonth,
                'propertyManagement' => $this->propertyManagement,
                'propertyManagementPercent' => $this->propertyManagementPercent,
                'realtPlatform' => $this->realtPlatform,
                'realtPlatformPercent' => $this->realtPlatformPercent,
                'insurance' => $this->insurance,
                'propertyTaxes' => $this->propertyTaxes,
                'utilities' => $this->utilities,
                'initialMaintenanceReserve' => $this->initialMaintenanceReserve,
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
                'rentedUnits' => $this->rentedUnits,
                'totalUnits' => $this->totalUnits,
                'termOfLease' => $this->termOfLease,
                'renewalDate' => $this->renewalDate,
                'section8paid' => $this->section8paid,
                'subsidyStatus' => $this->subsidyStatus,
                'subsidyStatusValue' => $this->subsidyStatusValue,
                'subsidyBy' => $this->subsidyBy,
                'sellPropertyTo' => $this->sellPropertyTo,
                'secondaryMarketplace' => $this->secondaryMarketplace,
                'secondaryMarketplaces' => $this->secondaryMarketplaces,
                'blockchainAddresses' => $this->blockchainAddresses,
                'underlyingAssetPrice' => $this->underlyingAssetPrice,
                'renovationReserve' => $this->renovationReserve,
                'propertyMaintenanceMonthly' => $this->propertyMaintenanceMonthly,
                'rentStartDate' => $this->rentStartDate,
                'lastUpdate' => $this->lastUpdate,
                'originSecondaryMarketplaces' => $this->originSecondaryMarketplaces,
                'initialLaunchDate' => $this->initialLaunchDate,
                'seriesNumber' => $this->seriesNumber,
                'constructionYear' => $this->constructionYear,
                'constructionType' => $this->constructionType,
                'roofType' => $this->roofType,
                'assetParking' => $this->assetParking,
                'foundation' => $this->foundation,
                'heating' => $this->heating,
                'cooling' => $this->cooling,
                'tokenIdRules' => $this->tokenIdRules,
                'rentCalculationType' => $this->rentCalculationType,
                'realtListingFeePercent' => $this->realtListingFeePercent,
                'realtListingFee' => $this->realtListingFee,
                'miscellaneousCosts' => $this->miscellaneousCosts,
                'propertyStories' => $this->propertyStories,
                'rentalType' => $this->rentalType
            ];
        } else {
            $response = [
                'fullName' => $this->fullName,
                'shortName' => $this->shortName,
                'symbol' => $this->symbol,
                'productType' => $this->productType,
                'tokenPrice' => $this->tokenPrice,
                'currency' => $this->currency,
                'uuid' => $this->uuid,
                'ethereumContract' => $this->ethereumContract,
                'xDaiContract' => $this->xDaiContract,
                'gnosisContract' => $this->gnosisContract,
                'lastUpdate' => $this->lastUpdate
            ];
        }

        if ($credentials['isAdmin']) {
            $response['originSecondaryMarketplaces'] = $this->originSecondaryMarketplaces;
        }

        return $response;
    }
}
