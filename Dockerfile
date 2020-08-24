FROM alpine:latest

RUN apk --no-cache add php7 php7-fpm php7-json php7-openssl php7-curl \
    php7-zlib php7-xml php7-phar php7-intl php7-dom php7-xmlreader php7-ctype php7-session \
    php7-mbstring php7-gd php7-simplexml php7-xmlwriter php7-tokenizer nginx supervisor curl \
    php7-mbstring php7-gd php7-simplexml php7-xmlwriter php7-tokenizer nginx curl \
    php7-pdo php7-pdo_pgsql php7-iconv php7-redis


RUN apk upgrade

RUN apk --no-cache add php7-pecl-amqp  php-zip
# Configure nginx
COPY config/nginx/nginx.conf /etc/nginx/nginx.conf
# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Configure PHP-FPM
COPY config/php/fpm-pool.conf /etc/php7/php-fpm.d/zzz_custom.conf
COPY config/php/php.ini /etc/php7/conf.d/zzz_custom.ini

# Copy run script
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Configure supervisord
COPY config/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Fix open() nginx.pid
RUN mkdir -p /run/nginx/
