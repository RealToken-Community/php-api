#FROM sigri44/docker-symfony-php7:latest
#FROM sigri44/docker-symfony-php8:latest
FROM registry.realtoken.community/docker-symfony-php:8

# Composer
WORKDIR /var/www/html
COPY . ./
RUN cp .env.dev .env
RUN composer install --prefer-dist --no-interaction --optimize-autoloader --no-progress

# HTTPS
ENV HTTPS false

# Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/blockips.conf /etc/nginx/blockips.conf

RUN mkdir -p var/cache/prod
RUN chmod -R 777 var/cache/prod

# PHP
## Remove fastcgi log debug
#RUN echo 'fastcgi.logging=0' >> "/etc/php7/php.ini"
#RUN echo 'fastcgi.logging=0' >> "/etc/php8/php.ini"

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
