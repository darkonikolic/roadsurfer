FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    icu-dev \
    bash \
    autoconf \
    g++ \
    make \
    zlib-dev \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql bcmath gd intl

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install PCOV for code coverage
RUN pecl install pcov && docker-php-ext-enable pcov

# Get latest Composer
COPY --from=composer:2.7.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Create www-data user and group if they don't exist
RUN addgroup -g 1000 www-data || true && \
    adduser -u 1000 -G www-data -s /bin/bash -D www-data || true

# Create necessary directories and set ownership
RUN mkdir -p /var/www/public /var/www/vendor && \
    chown -R www-data:www-data /var/www

# Copy init script
COPY init.sh /usr/local/bin/init.sh
RUN chmod +x /usr/local/bin/init.sh

# Expose port 9000
EXPOSE 9000

# Use init script as entrypoint
ENTRYPOINT ["/usr/local/bin/init.sh"]
CMD ["php-fpm"] 