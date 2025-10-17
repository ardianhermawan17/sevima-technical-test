FROM php:8.4-cli-bookworm AS builder
LABEL authors="Imanuddin Ardian Hermawan"

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libgmp-dev \
    curl \
    libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_pgsql pgsql gmp zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin --filename=composer

COPY . .

RUN composer validate --no-check-all --strict || true

RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

FROM php:8.4-cli-bookworm AS runtime

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpq-dev \
    tzdata \
    curl \
    git \
    libssl-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=builder /app ./

RUN addgroup --system app && adduser --system --ingroup app app \
    && chown -R app:app /var/www/html
USER app

EXPOSE 9000

HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD php -r "exit(extension_loaded('pdo_pgsql') ? 0 : 1);"

CMD ["php", "-S", "0.0.0.0:9000", "-t", "public"]
