# RealToken-API
RealToken-Community API for RealToken ecosystem.

## Installation
Run API Stack :
```bash
docker compose up -d
```

Create Database :
```
docker exec -it symfony php bin/console doctrine:database:create
```

Create Table :
```
docker exec -it symfony php bin/console doctrine:schema:create
```

Migrate :
```
docker exec -it symfony php bin/console doctrine:migrations:migrate
```
