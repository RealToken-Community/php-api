#!/bin/bash

WORKING_DIR=${PWD}

# Remove linked .env
rm current/.env*
# Copy real .env files
cp "${WORKING_DIR}"/shared/.env* current/

# Docker build
docker build .

# Docker run
docker-compose -f docker-compose.preprod.yml up -d --force

# Migrate
docker-compose -f docker-compose.preprod.yml exec -T symfony-preprod php bin/console doctrine:migrations:migrate -q