#!/bin/bash

WORKING_DIR=${PWD%/*}

# Remove linked .env
rm .env*
# Copy real .env files
cp "${WORKING_DIR}"/shared/.env* .

# Docker build
sudo docker build .

# Docker run
sudo docker-compose -f docker-compose.preprod.yml up -d --force

# Migrate
sudo docker-compose -f docker-compose.preprod.yml exec -T symfony-preprod php bin/console doctrine:migrations:migrate