# Build en local

```bash
docker compose -f docker-compose.local.yml up -d --force-recreate --build

Goto: http://localhost:9080/

# Install dB
docker exec -it api-sf php bin/console doctrine:schema:create
#docker exec -it api-sf php bin/console doctrine:schema:update --force

# Install migrations
docker exec -it api-sf php bin/console doctrine:migrations:migrate


# Try Redis cache load
wrk -t4 -c200 -d30s http://localhost:9080/api/tokens

# Upgrade Symfony version
docker exec -it api-sf php bin/console debug:container --deprecations
```
## Debug

```bash
## Get
composer why symfony/PACKAGE_NAME
```

### Tmp fix pour upgrade dB 5.3 -> 7.3 en Prod

```sql
ALTER TABLE tokenlist_integrity
    MODIFY COLUMN data LONGTEXT;

ALTER TABLE tokens
  MODIFY COLUMN coordinate LONGTEXT,
  MODIFY COLUMN image_link LONGTEXT,
  MODIFY COLUMN secondary_marketplace LONGTEXT,
  MODIFY COLUMN blockchain_addresses LONGTEXT,
  MODIFY COLUMN secondary_marketplaces LONGTEXT,
  MODIFY COLUMN origin_secondary_marketplaces LONGTEXT;
```

```bash
docker exec -it api-sf php temp_upgrade_sql.php
```
