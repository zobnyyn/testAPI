FROM php:8.1-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libssl-dev

# Установка расширений PHP
RUN docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath sockets

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Установка Node.js (для фронтенда, если понадобится)
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y nodejs

# Рабочая директория
WORKDIR /var/www

# Копируем только composer.json и composer.lock для ускорения установки зависимостей
COPY src/composer.* ./
# Копируем остальной код приложения
COPY src .
# Устанавливаем зависимости
RUN composer install --no-dev --optimize-autoloader

# Разрешения
RUN chown -R www-data:www-data /var/www/storage
RUN chmod -R 775 /var/www/storage

# Запуск Octane
CMD php artisan octane:start --host=0.0.0.0 --port=8000
