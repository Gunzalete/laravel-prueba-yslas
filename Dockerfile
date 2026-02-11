FROM php:8.2-cli

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        sqlite3 \
        libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*


COPY composer.json composer.lock* ./

# Ensure artisan and bootstrap files are available before running composer
# so Composer scripts like `@php artisan package:discover` can run.
COPY artisan bootstrap ./

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy the rest of the application
COPY . .

# Run autoload dump and package discovery now that the full app (including
# bootstrap and artisan) is available in the image and vendor exists.
RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi || true

RUN chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

CMD ["entrypoint.sh"]
