#!/bin/bash

WORKING_DIR="/home/realt/docker/api/preprod"

# Remove linked .env
rm .env*
# Copy real .env files
cp "${WORKING_DIR}"/shared/.env* .

# Docker build
docker build .

# Docker run
docker-compose -f docker-compose.preprod.yml up -d --force symfony-preprod

# Migrate
docker-compose -f docker-compose.preprod.yml exec -T symfony-preprod php bin/console doctrine:migrations:migrate -q