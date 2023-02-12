ARG PHP_VERSION="8.1.12"
FROM php:${PHP_VERSION}-fpm
RUN apt-get update && apt-get -y --no-install-recommends install \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    supervisor \
    cron\
    && apt-get autoremove --purge -y && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
RUN docker-php-ext-install -j$(nproc) \
    opcache \
    intl \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    sockets

RUN pecl install apcu-5.1.20 && docker-php-ext-enable apcu
COPY docker/app/php.ini   $PHP_INI_DIR/conf.d/
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /var/www/app
COPY . /var/www/app
RUN composer update
ADD docker/app/supervisor.conf /etc/supervisor/conf.d/supervisord.conf

# copy file crontab ke direktori cron.d
ADD docker/crontab/crontab /etc/cron.d/crontab

# beri akses eksekusi crontab
RUN chmod 0644 /etc/cron.d/crontab

# apply cron job
RUN crontab /etc/cron.d/crontab

# buat log file
RUN touch /var/log/cron.log

# jalankan command cron ketika container sudah berjalan
# CMD cron && tail -f /var/log/cron.log

CMD cron && docker-php-entrypoint php-fpm
