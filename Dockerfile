FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    apache2 \
    php8.1 \
    php8.1-cli \
    php8.1-common \
    php8.1-mysql \
    php8.1-zip \
    php8.1-gd \
    php8.1-mbstring \
    php8.1-curl \
    php8.1-xml \
    php8.1-bcmath \
    php8.1-bz2 \
    php8.1-calendar \
    php8.1-exif \
    php8.1-ftp \
    php8.1-gettext \
    php8.1-mysqli \
    php8.1-pdo-mysql \
    php8.1-pdo-sqlite \
    php8.1-phar \
    php8.1-readline \
    php8.1-fileinfo \
    php8.1-simplexml \
    libapache2-mod-php8.1 \
    curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

RUN echo '<Directory /var/www/html>\n\
    Options FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/gymone.conf \
    && a2enconf gymone

RUN echo "memory_limit = 256M" > /etc/php/8.1/apache2/conf.d/gymone.ini && \
    echo "upload_max_filesize = 50M" >> /etc/php/8.1/apache2/conf.d/gymone.ini && \
    echo "post_max_size = 50M" >> /etc/php/8.1/apache2/conf.d/gymone.ini && \
    echo "max_execution_time = 300" >> /etc/php/8.1/apache2/conf.d/gymone.ini

WORKDIR /var/www/html

COPY . .

RUN chmod -R 777 /var/www/html \
    && chown -R www-data:www-data /var/www/html

RUN echo '#!/bin/bash\n\
chmod -R 777 /var/www/html\n\
chown -R www-data:www-data /var/www/html\n\
service apache2 start\n\
tail -f /var/log/apache2/error.log' > /entrypoint.sh \
    && chmod +x /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]
