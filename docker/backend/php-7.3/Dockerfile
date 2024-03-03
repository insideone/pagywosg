FROM php:7.3.0-fpm

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    iputils-ping \
    libicu-dev \
    libmemcached-dev \
    libz-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libmcrypt-dev \
    libxml2-dev \
    libbz2-dev \
    libjpeg62-turbo-dev \
    librabbitmq-dev \
    libzip-dev \
    curl \
    git \
    subversion \
    unzip \
  && rm -rf /var/lib/apt/lists/*

# Install various PHP extensions
RUN docker-php-ext-configure bcmath --enable-bcmath \
  && docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-configure pdo_mysql --with-pdo-mysql \
  && docker-php-ext-configure pdo_pgsql --with-pgsql \
  && docker-php-ext-configure mbstring --enable-mbstring \
  && docker-php-ext-configure soap --enable-soap \
  && docker-php-ext-install \
    bcmath \
    intl \
    mbstring \
    mysqli \
    pcntl \
    pdo_mysql \
    pdo_pgsql \
    soap \
    sockets \
    zip \
  && docker-php-ext-configure gd \
    --enable-gd-native-ttf \
    --with-jpeg-dir=/usr/lib \
    --with-freetype-dir=/usr/include/freetype2 \
  && docker-php-ext-install gd \
  && docker-php-ext-install opcache \
  && docker-php-ext-enable opcache \
  && pecl install amqp \
  && docker-php-ext-enable amqp


# ICU - intl requirements for Symfony
# Debian is out of date, and Symfony expects the latest - so build from source, unless a better alternative exists(?)
RUN curl -sS -o /tmp/icu.tar.gz -L https://github.com/unicode-org/icu/releases/download/release-58-3/icu4c-58_3-src.tgz \
	&& tar -zxf /tmp/icu.tar.gz -C /tmp \
	&& cd /tmp/icu/source \
	&& ./configure --prefix=/usr/local \
	&& make \
	&& make install

RUN docker-php-ext-configure intl \
    --with-icu-dir=/usr/local \
  && docker-php-ext-install intl


# Install the php memcached extension
RUN curl -L -o /tmp/memcached.tar.gz "https://github.com/php-memcached-dev/php-memcached/archive/v3.1.3.tar.gz" \
  && mkdir -p memcached \
  && tar -C memcached -zxvf /tmp/memcached.tar.gz --strip 1 \
  && ( \
    cd memcached \
    && phpize \
    && ./configure \
    && make -j$(nproc) \
    && make install \
  ) \
  && rm -r memcached \
  && rm /tmp/memcached.tar.gz \
  && docker-php-ext-enable memcached

# Install xdebug - no stable version for 7.3 yet
RUN pecl install xdebug-2.7.0beta1 \
    && docker-php-ext-enable xdebug

# disable xdebug by default because of huge lack of performance
RUN mv /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini.off

# Copy opcache configration
COPY ./opcache.ini /usr/local/etc/php/conf.d/91-opcache.ini

# Custom php settings
COPY ./php.ini /usr/local/etc/php/conf.d/90-php.ini

ENV COMPOSER_HOME /var/.composer
ARG PROJECT_DIR_ARG='/var/www'
ENV PROJECT_DIR=$PROJECT_DIR_ARG

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN chown -R www-data:www-data /usr/bin
RUN chown -R www-data:www-data $PROJECT_DIR
RUN mkdir $COMPOSER_HOME
RUN chown -R www-data:www-data $COMPOSER_HOME

USER www-data

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin \
    --filename=composer \
  && composer self-update

RUN rm -rf /var/lib/apt/lists/*

VOLUME $COMPOSER_HOME

COPY ./entrypoint.sh /entrypoint.sh

# XDebug
# This value must match the name of the 'server' created in PhpStorm for XDebug purposes
# https://confluence.jetbrains.com/display/PhpStorm/Debugging+PHP+CLI+scripts+with+PhpStorm#DebuggingPHPCLIscriptswithPhpStorm-2.StarttheScriptwithDebuggerOptions
ENV PHP_IDE_CONFIG "serverName=Docker"
ENV PATH=/:$PROJECT_DIR/vendor/bin/:$PROJECT_DIR/bin/:$PATH

WORKDIR $PROJECT_DIR
