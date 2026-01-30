# BASE STAGE
FROM php:7.4.33-apache AS base

# START - INSTALL PECL DEPENDENCIES (EXECUTE THIS ALWAYS BEFORE COPY files)

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        zlib1g-dev \
        libzip-dev \
        && \
#   START - PLACE PECL DEPENDENCY INSTALL TO AVOID CONSTANTS DOWNLOADS ON EACH CONTAINER RESTART
    pecl install channel://pecl.php.net/swoole-4.8.13 && \
    docker-php-ext-enable swoole && \
#   END - PLACE PECL DEPENDENCY INSTALL TO AVOID CONSTANTS DOWNLOADS ON EACH CONTAINER RESTART
    apt-get purge -y zlib1g-dev libzip-dev && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# END - INSTALL PECL DEPENDENCIES

# Copy app files from the app directory.
COPY . /var/www/html/

# Set working directory (recommended)
WORKDIR /var/www/html/

# START - SWOOLE SETTINGS

RUN chmod +x ./src/swoole.php

# END - SWOOLE SETTINGS

# START - PHP.INI OVERWRITE

RUN chmod +x src/z-php.ini && mkdir -p /usr/local/etc/php/conf.d/
COPY src/z-php.ini /usr/local/etc/php/conf.d/

# END - PHP.INI OVERWRITE

# START - APACHE OVERWRITE

RUN cp /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/

# Copy custom configuration
COPY ./src/000-default.conf /etc/apache2/sites-available/000-default.conf

# END - APACHE OVERWRITE

# DEVELOPMENT STAGE
FROM base AS development

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# START - XDEBUG SETTINGS

# Install and Configure Xdebug
RUN apt-get update && \
    apt-get install -y --no-install-recommends zlib1g-dev libzip-dev && \
    pecl install xdebug-3.1.6 && \
    docker-php-ext-enable xdebug && \
    apt-get purge -y zlib1g-dev libzip-dev && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log_level=7" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.idekey=VSCODE" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# END - XDEBUG SETTINGS

# Switch to a non-privileged user (defined in the base image) that the app will run under.
# See https://docs.docker.com/go/dockerfile-user-best-practices/
USER www-data

# PRODUCTION STAGE
FROM base AS production

# Use the default production configuration for PHP runtime arguments, see
# https://github.com/docker-library/docs/tree/master/php#configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Switch to a non-privileged user (defined in the base image) that the app will run under.
# See https://docs.docker.com/go/dockerfile-user-best-practices/
USER www-data