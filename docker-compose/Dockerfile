FROM php:8.2-fpm

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www


# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    redis \
    redis-server \
    gnupg \
    curl

# Install Node JS
RUN curl -sL https://deb.nodesource.com/setup_18.x  | bash -
RUN apt-get -y install nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

## Install composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
#
## Create system user to run Composer and Artisan Commands
#RUN useradd -G www-data,root -u $uid -d /home/$user $user
#RUN mkdir -p /home/$user/.composer && \
#    chown -R $user:$user /home/$user && \
#    chown -R $user /var/www/
#
#USER $user

EXPOSE 9000
CMD ["php-fpm"]
