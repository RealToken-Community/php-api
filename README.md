# RealT-API
RealT-Community API for RealTokens

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
