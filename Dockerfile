FROM php:8.1-apache
# Install mysqli and pdo_mysql extensions so PHP can connect to MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql
