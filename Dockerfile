FROM php:8.3-cli

# Install necessary packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    && docker-php-ext-install curl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set Composer environment variable to allow running as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /app

# Copy existing application directory contents
COPY . /app

# Install PHP dependencies including require-dev
RUN composer install --no-interaction

# Install PHP-CS-Fixer globally
RUN composer global require friendsofphp/php-cs-fixer

# Add Composer global bin to PATH
ENV PATH="/root/.composer/vendor/bin:${PATH}"

# Run PHP unit tests
CMD ["vendor/bin/phpunit", "--configuration", "phpunit.xml", "--colors=always"]
