# syntax=docker/dockerfile:1

# =========================================================================
# SISWA-TER — Production image (FrankenPHP + Laravel)
# Multi-stage build: PHP deps -> frontend assets -> final runtime image
# =========================================================================

# ---- Base: FrankenPHP runtime + PHP extensions ----
FROM dunglas/frankenphp:php8.3 AS base
WORKDIR /app
RUN install-php-extensions \
        pdo_sqlite \
        mbstring \
        intl \
        zip \
        gd \
        bcmath \
        opcache \
        pcntl
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---- Vendor: install PHP dependencies (no dev, no scripts) ----
FROM base AS vendor
RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip \
 && rm -rf /var/lib/apt/lists/*
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts

# ---- Assets: compile frontend with Vite/Tailwind ----
FROM node:22 AS assets
WORKDIR /app
COPY package.json ./
RUN npm install
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# ---- App: final production image ----
FROM base AS app

ENV APP_ENV=production \
    APP_DEBUG=false \
    SERVER_NAME=":80" \
    LOG_CHANNEL=stderr

# Application source
COPY . .
# Production vendor + compiled assets from previous stages
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --no-dev --optimize --no-scripts \
 && mkdir -p \
        storage/framework/cache \
        storage/framework/views \
        storage/framework/sessions \
        storage/logs \
        bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

# Health endpoint provided by Laravel (bootstrap/app.php -> health: '/up')
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD php -r "exit(@file_get_contents('http://127.0.0.1:'.(getenv('PORT') ?: '80').'/up') === false ? 1 : 0);"

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

ENTRYPOINT ["entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
