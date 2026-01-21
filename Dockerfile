FROM php:8.3-cli

# Install build tools and Xdebug for coverage
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        autoconf \
        g++ \
        make \
        git \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=coverage" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=off" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && apt-get purge -y --auto-remove autoconf g++ make \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Default command keeps the container running for exec into
CMD ["tail", "-f", "/dev/null"]
