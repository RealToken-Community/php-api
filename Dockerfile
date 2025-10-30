# FROM registry.realtoken.community/docker-symfony-php:8.4
#
# WORKDIR /var/www/html
# COPY . ./
#
# ARG APP_ENV=prod
# ARG DATABASE_URL
# ENV APP_ENV=${APP_ENV}
# ENV DATABASE_URL=${DATABASE_URL}
#
# RUN composer install --prefer-dist --no-interaction --optimize-autoloader --no-progress
# RUN composer dump-env ${APP_ENV}
# RUN composer run-script --no-dev post-install-cmd
#
# # HTTPS
# ENV HTTPS=false
#
# # Nginx
# COPY docker/nginx.conf /etc/nginx/nginx.conf
#
# RUN mkdir -p var/cache/${APP_ENV}
# RUN chmod -R 777 var/cache/${APP_ENV}
#
# CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]


ARG BASE_IMAGE=dunglas/frankenphp:1-php8.4
# ===============================================
# Stage 1: Dependencies
# ===============================================
FROM composer:2 AS dependencies

WORKDIR /app

# Copie uniquement les fichiers de dépendances pour cache Docker
COPY composer.json composer.lock symfony.lock ./

# Installation des dépendances
RUN composer install \
    --no-scripts \
    --no-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist

# ===============================================
# Stage 2: Development
# ===============================================
FROM ${BASE_IMAGE} AS development

# Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installation des extensions PHP nécessaires
# On nettoie le cache APT pour libérer de l'espace
RUN apt-get clean && rm -rf /var/lib/apt/lists/* && \
	install-php-extensions \
		pdo_mysql \
		redis \
		intl \
		sysvsem \
	&& apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copier les dépendances
COPY --from=dependencies /app/vendor ./vendor

# Copier le code
COPY --chown=www-data:www-data . .

# Installer avec dev dependencies
RUN composer install \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

# Permissions correctes
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var/ \
    && chmod -R 775 var/

# Créer les répertoires nécessaires pour Caddy/FrankenPHP
RUN mkdir -p /data/caddy /config/caddy \
    && chown -R www-data:www-data /data/caddy /config/caddy \
    && chmod -R 755 /data/caddy /config/caddy

# Copier la configuration Caddy
COPY docker/Caddyfile /etc/caddy/Caddyfile

# Variables d'environnement dev
ENV APP_ENV=dev
ENV APP_DEBUG=1
ENV FRANKENPHP_CONFIG="worker ./public/index.php"

USER www-data

EXPOSE 80

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]

# ===============================================
# Stage 3: Builder (préparation production)
# ===============================================
FROM dependencies AS builder

WORKDIR /app

# Copier tout le code source
COPY . .

# Installation des dépendances SANS dev
RUN composer install \
    --no-dev \
    --no-scripts \
    --optimize-autoloader \
    --classmap-authoritative \
    --no-interaction \
    --no-progress \
    --prefer-dist

# Optimisations Symfony pour production
RUN composer dump-autoload --no-dev --classmap-authoritative

# Suppression des fichiers inutiles en production
RUN rm -rf tests/ .git/ .github/ docker/ \
    && find . -name ".git*" -type f -delete

# ===============================================
# Stage 4: Production
# ===============================================
FROM dunglas/frankenphp:1-php8.3 AS production

# Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installation des extensions PHP
RUN apt-get clean && rm -rf /var/lib/apt/lists/* && \
	install-php-extensions \
		pdo_mysql \
		redis \
		intl \
		apcu \
		sysvsem \
	&& apt-get clean && rm -rf /var/lib/apt/lists/*

# Configuration PHP production
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/99-prod.ini

WORKDIR /app

# Copier depuis le builder (code optimisé)
COPY --from=builder --chown=www-data:www-data /app /app

# Configuration Caddy production
COPY docker/Caddyfile.prod /etc/caddy/Caddyfile

# Permissions strictes
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app \
    && mkdir -p var/cache/prod var/log \
    && chown -R www-data:www-data var/ \
    && chmod -R 775 var/

# Créer les répertoires nécessaires pour Caddy/FrankenPHP
RUN mkdir -p /data/caddy /config/caddy \
    && chown -R www-data:www-data /data/caddy /config/caddy \
    && chmod -R 755 /data/caddy /config/caddy

# Variables d'environnement production
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV FRANKENPHP_CONFIG="worker ./public/index.php"

# Warmup du cache Symfony
RUN php bin/console cache:clear --env=prod --no-debug || true \
    && php bin/console cache:warmup --env=prod --no-debug || true

USER www-data

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]