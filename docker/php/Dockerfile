FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libssl-dev libfreetype6-dev libcurl3-dev libjpeg62-turbo-dev  \
    && apt-get install -y libcurl4-openssl-dev zlib1g-dev zip unzip git  libpq-dev libzip-dev \
    && apt-get install -y software-properties-common build-essential libprotobuf-dev libboost-dev openssl \
    && apt-get install -y protobuf-compiler liblz4-tool zstd libgdbm-dev  libffi-dev  \
    && apt-get install -y wget libbz2-dev --no-install-recommends apt-utils \
    && docker-php-ext-install pdo pdo_mysql mysqli  && docker-php-ext-enable mysqli \
    && docker-php-ext-install -j$(nproc) zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install -j$(nproc) exif \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && rm -rf /var/lib/apt/lists/*


# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- \
	--install-dir=/usr/local/bin \
	--filename=composer
