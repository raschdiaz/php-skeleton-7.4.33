# DEVELOPMENT STAGE

# syntax=docker/dockerfile:1

# Comments are provided throughout this file to help you get started.
# If you need more help, visit the Dockerfile reference guide at
# https://docs.docker.com/go/dockerfile-reference/

# Want to help us make this template better? Share your feedback here: https://forms.gle/ybq9Krt8jtBL3iCk7

################################################################################

# The example below uses the PHP Apache image as the foundation for running the app.
# By specifying the "7.4.33-apache" tag, it will also use whatever happens to be the
# most recent version of that tag when you build your Dockerfile.
# If reproducibility is important, consider using a specific digest SHA, like
# php@sha256:99cede493dfd88720b610eb8077c8688d3cca50003d76d1d539b0efc8cca72b4.
FROM php:7.4.33-apache AS development

# Your PHP application may require additional PHP extensions to be installed
# manually. For detailed instructions for installing extensions can be found, see
# https://github.com/docker-library/docs/tree/master/php#how-to-install-more-php-extensions
# The following code blocks provide examples that you can edit and use.
#
# Add core PHP extensions, see
# https://github.com/docker-library/docs/tree/master/php#php-core-extensions
# This example adds the apt packages for the 'gd' extension's dependencies and then
# installs the 'gd' extension. For additional tips on running apt-get:
# https://docs.docker.com/go/dockerfile-aptget-best-practices/
# RUN apt-get update && apt-get install -y \
#     libfreetype-dev \
#     libjpeg62-turbo-dev \
#     libpng-dev \
# && rm -rf /var/lib/apt/lists/* \
#     && docker-php-ext-configure gd --with-freetype --with-jpeg \
#     && docker-php-ext-install -j$(nproc) gd
#
# Add PECL extensions, see
# https://github.com/docker-library/docs/tree/master/php#pecl-extensions
# This example adds the 'redis' and 'xdebug' extensions.
# RUN pecl install redis-5.3.7 \
#    && pecl install xdebug-3.2.1 \
#    && docker-php-ext-enable redis xdebug

# Use the default production configuration for PHP runtime arguments, see
# https://github.com/docker-library/docs/tree/master/php#configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# START - INSTALL PECL DEPENDENCIES (EXECUTE THIS ALWAYS BEFORE COPY files)

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        zlib1g-dev \
        libzip-dev \
        && \
#   START - PLACE PECL DEPENDENCY INSTALL TO AVOID CONSTANTS DOWNLOADS ON EACH CONTAINER RESTART
    pecl install channel://pecl.php.net/swoole-4.8.13 xdebug-3.1.6 && \
    docker-php-ext-enable swoole xdebug && \
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

# START - XDEBUG SETTINGS

#RUN apt-get update && apt-get install -y iproute2   

# Configure Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log_level=7" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.idekey=VSCODE" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# END - XDEBUG SETTINGS

# START - PHP.INI OVERWRITE

RUN chmod +x src/z-php.ini && mkdir -p /usr/local/etc/php/conf.d/
COPY src/z-php.ini /usr/local/etc/php/conf.d/

#RUN mkdir -p /var/log && chown -R www-data:www-data /var/log && touch /var/log/php-errors.log && chmod 777 /var/log/php-errors.log

# END - PHP.INI OVERWRITE

# START - APACHE OVERWRITE

RUN cp /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/

# Copy custom configuration
COPY ./src/000-default.conf /etc/apache2/sites-available/000-default.conf

# Enable the site by creating a symlink (NOT REQUIRED, symlink already created)
# RUN ln -s /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf

# END - APACHE OVERWRITE

# Switch to a non-privileged user (defined in the base image) that the app will run under.
# See https://docs.docker.com/go/dockerfile-user-best-practices/
USER www-data

#PRODUCTION STAGE

FROM php:7.4.33-apache AS production

# Use the default production configuration for PHP runtime arguments, see
# https://github.com/docker-library/docs/tree/master/php#configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# START - INSTALL PECL DEPENDENCIES (EXECUTE THIS ALWAYS BEFORE COPY files)

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        zlib1g-dev \
        libzip-dev \
        && \
#   START - PLACE PECL DEPENDENCY INSTALL TO AVOID CONSTANTS DOWNLOADS ON EACH CONTAINER RESTART
    pecl install channel://pecl.php.net/swoole-4.8.13 xdebug-3.1.6 && \
    docker-php-ext-enable swoole xdebug && \
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

# START - XDEBUG SETTINGS

#RUN apt-get update && apt-get install -y iproute2   

# Configure Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log_level=7" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.idekey=VSCODE" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# END - XDEBUG SETTINGS

# START - PHP.INI OVERWRITE

RUN chmod +x src/z-php.ini && mkdir -p /usr/local/etc/php/conf.d/
COPY src/z-php.ini /usr/local/etc/php/conf.d/

#RUN mkdir -p /var/log && chown -R www-data:www-data /var/log && touch /var/log/php-errors.log && chmod 777 /var/log/php-errors.log

# END - PHP.INI OVERWRITE

# START - APACHE OVERWRITE

RUN cp /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/

# Copy custom configuration
COPY ./src/000-default.conf /etc/apache2/sites-available/000-default.conf

# Enable the site by creating a symlink (NOT REQUIRED, symlink already created)
# RUN ln -s /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf

# END - APACHE OVERWRITE

# Switch to a non-privileged user (defined in the base image) that the app will run under.
# See https://docs.docker.com/go/dockerfile-user-best-practices/
USER www-data