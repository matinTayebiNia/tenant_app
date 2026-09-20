# --- Stage 2: PHP application, served by Octane over FrankenPHP ------------
FROM dunglas/frankenphp:1-php8.3 AS app

WORKDIR /app

# System packages + PHP extensions this app actually needs:
# - pdo_mysql: config/database.php's `mysql` connection
# - redis: no predis package is installed, so the app expects the phpredis extension
# - pcntl: required by Octane for worker signal handling
# - bcmath, intl, zip, gd: standard Laravel/media-handling needs
# - opcache: production performance
# - sockets: required by spiral/goridge + spiral/roadrunner-worker (Octane's default Roadrunner
#   scaffolding, still in composer.lock even though this app runs FrankenPHP, not Roadrunner)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libicu-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        unzip \
        curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && rm -rf /var/lib/apt/lists/*

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions pdo_mysql pcntl bcmath intl zip gd opcache sockets ftp

# pecl.php.net's release-metadata API and `git clone` against github.com are both unreachable
# from some build networks (this one included) — codeload.github.com and api.github.com are
# reachable, so fetch the phpredis tag tarball plus its pinned `liblzf` submodule commit as plain
# tarballs instead of using git at all.
RUN LIBLZF_SHA=$(curl -fsSL https://api.github.com/repos/phpredis/phpredis/contents/liblzf?ref=6.1.0 | grep -o '"sha": *"[0-9a-f]*"' | head -1 | grep -o '[0-9a-f]\{40\}') \
    && mkdir -p /tmp/phpredis/liblzf \
    && curl -fsSL "https://codeload.github.com/phpredis/phpredis/tar.gz/refs/tags/6.1.0" | tar xz --strip-components=1 -C /tmp/phpredis \
    && curl -fsSL "https://codeload.github.com/nemequ/liblzf/tar.gz/${LIBLZF_SHA}" | tar xz --strip-components=1 -C /tmp/phpredis/liblzf \
    && cd /tmp/phpredis \
    && phpize \
    && ./configure \
    && make -j"$(nproc)" \
    && make install \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/phpredis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# require-dev (phpunit, pint, fakerphp/faker — factories need Faker to run) is installed for
# every APP_ENV except `production`, so `php artisan test`, `db:seed`, and factories all work
# out of the box in local/staging images, while a real production build stays lean.
ARG APP_ENV=local

# Install dependencies first (cache-friendly layer, ahead of the full source copy).
COPY composer.json composer.lock ./
RUN if [ "$APP_ENV" = "production" ]; then \
        composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist; \
    else \
        composer install --no-scripts --no-autoloader --no-interaction --prefer-dist; \
    fi

# Now bring in the full application source.
COPY . .

RUN if [ "$APP_ENV" = "production" ]; then \
        composer dump-autoload --optimize --no-dev; \
    else \
        composer dump-autoload --optimize; \
    fi \
    && php artisan package:discover --ansi

# Empty placeholder, never real values (.env itself stays out of the image — see .dockerignore).
# Real config comes entirely from docker-compose's `env_file: .env` at container runtime. Without
# this, Laravel's dotenv loader still tries file_get_contents('.env') and emits a warning on every
# request/command since the file is genuinely absent from the image.
RUN touch .env

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["entrypoint.sh"]
