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
