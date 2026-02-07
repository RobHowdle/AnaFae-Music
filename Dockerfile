# syntax=docker/dockerfile:1

FROM node:20-alpine AS assets
WORKDIR /app

COPY package.json package-lock.json ./
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm ci
RUN npm run build


FROM php:8.4-fpm-bookworm AS app
WORKDIR /var/www/html

# System deps
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    git \
    pkg-config \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libicu-dev \
    libzip-dev \
    libonig-dev \
  && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    intl \
    mbstring \
    opcache \
    pdo \
    pdo_sqlite \
    zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# App code
COPY . .

# Frontend build output
COPY --from=assets /app/public/build ./public/build

# PHP deps
RUN composer install --no-dev --no-interaction --no-ansi --no-progress --prefer-dist --optimize-autoloader

# Nginx (Debian nginx uses sites-available/sites-enabled)
RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/sites-available/default
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Supervisor
COPY docker/supervisord.conf /etc/supervisord.conf

# Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
  && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
  && find /var/www/html/public -type d -exec chmod 755 {} + \
  && find /var/www/html/public -type f -exec chmod 644 {} +

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
