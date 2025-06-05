FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    curl \
    libxml2-dev \
    icu-dev \
    oniguruma-dev \
    zlib-dev \
    postgresql-dev \
    libzip-dev \
    unzip \
    rabbitmq-c-dev \
    autoconf \
    g++ \
    make \
    linux-headers \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install amqp \
    && pecl install xdebug \
    && docker-php-ext-enable amqp xdebug \
    && docker-php-ext-install \
        intl \
        pdo_pgsql \
        opcache \
        zip \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY etc/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
COPY etc/php/custom.ini /usr/local/etc/php/conf.d/custom.ini
ENTRYPOINT ["entrypoint.sh"]

RUN mkdir -p /app/var/log && \
    mkdir -p /var/log/symfony && \
    chown -R www-data:www-data /app/var/log && \
    chown -R www-data:www-data /var/log/symfony && \
    chmod -R 777 /app/var/log && \
    chmod -R 777 /var/log/symfony

WORKDIR /app
