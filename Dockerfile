FROM sigri44/docker-symfony:latest

# Composer
WORKDIR /var/www/html
COPY . ./
RUN cp .env.testing .env
RUN composer install --no-dev --prefer-dist --no-interaction --no-suggest --optimize-autoloader --no-progress
RUN mv .env.prod .env
#RUN php bin/console doctrine:schema:update --force

ENV HTTPS false

# Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p var/cache/prod
RUN chmod -R 777 var/cache/prod

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
