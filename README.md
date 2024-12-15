# RealToken PHP-API
RealToken-Community API for RealTokens

[![Alpha](https://github.com/RealToken-Community/php-api/actions/workflows/alpha.yml/badge.svg)](https://github.com/RealToken-Community/php-api/actions/workflows/alpha.yml)

## Installation
Build Container :
```bash
sudo docker-compose build
```

Run API Stack :
```bash
sudo docker-compose up -d
```

Create Database :
```
sudo docker-compose exec -T symfony php bin/console doctrine:database:create
```

Create Table :
```
sudo docker-compose exec -T symfony php bin/console doctrine:schema:update --force
```

Migrate :
```
sudo docker-compose exec -T symfony php bin/console doctrine:migrations:migrate
```
