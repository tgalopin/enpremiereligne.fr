FROM composer:1.9
FROM php:7.4-fpm-alpine

ENV APCU_VERSION=5.1.18 \
    REDIS_VERSION=5.1.1 \
    COMPOSER_ALLOW_SUPERUSER=1

RUN apk add --no-cache --virtual .persistent-deps \
        git \
        gmp-dev \
        icu-libs \
        libpq \
        make \
        nano \
        nginx \
        postgresql-client \
        python \
        py-pip \
        zlib

RUN set -xe \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        freetype \
        freetype-dev \
        icu-dev \
        libxslt-dev \
        postgresql-dev \
        libzip-dev \
    && docker-php-ext-install \
        bcmath \
        exif \
        gmp \
        intl \
        pcntl \
        pdo_pgsql \
        xsl \
        zip \
    && pecl install \
        apcu-${APCU_VERSION} \
        redis-${REDIS_VERSION} \
	&& docker-php-ext-enable --ini-name 05-opcache.ini opcache \
    && docker-php-ext-enable --ini-name 80-apcu.ini apcu \
	&& docker-php-ext-enable --ini-name 85-redis.ini redis \
    && runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )" \
    && apk add --no-cache --virtual .php-phpexts-rundeps $runDeps \
    && apk del .build-deps \
    && pip install supervisor \
    && mkdir -p /run/nginx/

COPY --from=0 /usr/bin/composer /usr/bin/composer
COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /usr/local/etc/supervisord.conf

WORKDIR /app
COPY . /app

ENV APP_ENV=prod \
    APP_DEBUG=0 \
    DATABASE_URL="postgresql://main:main@127.0.0.1:5432/main?serverVersion=12&charset=utf8" \
    SENTRY_DSN="https://xxx@sentry.io/xxx"

RUN mkdir -p var && \
    composer install --prefer-dist --no-interaction --no-ansi --no-autoloader --no-scripts --no-progress --no-suggest && \
    composer clear-cache && \
    composer dump-autoload --optimize --classmap-authoritative && \
    bin/console cache:clear --no-warmup && \
    bin/console cache:warmup && \
    bin/console assets:install && \
    chmod -R 777 var

CMD ["supervisord", "--configuration", "/usr/local/etc/supervisord.conf"]
