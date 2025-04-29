# change to alpine
FROM php:8.3.19-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    zip \
    libzip-dev \
    oniguruma-dev \
    icu-dev

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    intl \
    gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# mounting a volume instead (live reload)
# COPY . /app

WORKDIR /app
# CMD ["php", "-S", "0.0.0.0:3000", "-t", "public"]
CMD ["php-fpm"]

# a command for keeping the container alive
# CMD ["tail", "-f", "/dev/null"]