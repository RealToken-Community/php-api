FROM registry.realtoken.community/docker-symfony-php:8.4

WORKDIR /var/www/html
COPY . ./

ARG APP_ENV=prod
ARG DATABASE_URL
ENV APP_ENV=${APP_ENV}
ENV DATABASE_URL=${DATABASE_URL}

RUN composer install --prefer-dist --no-interaction --optimize-autoloader --no-progress
RUN composer dump-env ${APP_ENV}
RUN composer run-script --no-dev post-install-cmd

# HTTPS
ENV HTTPS=false

# Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p var/cache/${APP_ENV}
RUN chmod -R 777 var/cache/${APP_ENV}

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
