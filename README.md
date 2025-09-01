# RealToken PHP-API
RealToken-Community API for RealTokens

[![Alpha](https://github.com/RealToken-Community/php-api/actions/workflows/alpha.yml/badge.svg)](https://github.com/RealToken-Community/php-api/actions/workflows/alpha.yml)

## Local installation
```bash
docker build . -t api-php --no-cache
docker compose -f docker-compose.local.yml up --force-recreate

# For macos users
docker build . -t api-php --no-cache --platform=linux/amd64
docker compose -f docker-compose.local.yml up --force-recreate
```

Go to :
- API : `http://localhost:9080`
- Adminer : `http://localhost:18080`

On macos

Create Database :
```
docker exec -it api-sf php bin/console doctrine:database:create
```

Create Table (don't need if you migrate after) :
```
docker exec -it api-sf php bin/console doctrine:schema:update --force
```

Migrate :
```
docker exec -it api-sf php bin/console doctrine:migrations:migrate
```
