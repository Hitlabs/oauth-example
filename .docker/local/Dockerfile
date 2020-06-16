FROM php:7.3-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

WORKDIR /var/www/html
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN apt-get update -y && apt-get install -y openssl zip unzip git vim curl libpng-dev libzip-dev
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN docker-php-ext-install pdo pdo_mysql zip gd
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html
COPY .env.docker /var/www/html/.env

RUN composer install
RUN composer dump-autoload
RUN php artisan key:generate

RUN curl -sL https://deb.nodesource.com/setup_13.x | bash -
RUN apt-get install nodejs -y
RUN npm install --global cross-env
RUN npm install
RUN npm run production

COPY ./wait-for-it.sh /wait-for-it.sh
RUN chmod 777 /wait-for-it.sh

RUN chown -R www-data:www-data /var/www && a2enmod rewrite headers
CMD ["/usr/sbin/apachectl", "-D", "FOREGROUND"]
