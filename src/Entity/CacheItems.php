<?php

namespace App\Entity;

use App\Repository\CacheItemsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// CREATE TABLE cache_items (item_id VARBINARY(255) NOT NULL, item_data MEDIUMBLOB NOT NULL, item_lifetime INT UNSIGNED DEFAULT NULL, item_time INT UNSIGNED NOT NULL, PRIMARY KEY(item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''

#[ORM\Entity(repositoryClass: CacheItemsRepository::class)]
class CacheItems
{
  #[ORM\Id, ORM\Column(type: Types::BINARY, length: 255)]
  private string $item_id;

  #[ORM\Column(type: Types::BLOB, columnDefinition: 'MEDIUMBLOB NOT NULL')]
  private mixed $item_data;

  #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['unsigned' => true])]
  private ?int $item_lifetime;

  #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
  private int $item_time;

  public function getItemId(): string
  {
    return $this->item_id;
  }

  public function setItemId(string $item_id): static
  {
    $this->item_id = $item_id;

    return $this;
  }

  public function getItemData(): mixed
  {
    return $this->item_data;
  }

  public function setItemData(mixed $item_data): static
  {
    $this->item_data = $item_data;

    return $this;
  }

  public function getItemLifetime(): ?int
  {
    return $this->item_lifetime;
  }

  public function setItemLifetime(?int $item_lifetime): static
  {
    $this->item_lifetime = $item_lifetime;

    return $this;
  }

  public function getItemTime(): int
  {
    return $this->item_time;
  }

  public function setItemTime(int $item_time): static
  {
    $this->item_time = $item_time;

    return $this;
  }
}
