FROM php:apache
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY ./src .
EXPOSE 80
