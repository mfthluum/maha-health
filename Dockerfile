FROM php:8.2-apache

# Install ekstensi MySQL untuk PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Copy seluruh kodingan ke folder web server
COPY . /var/www/html/

# Expose port 80
EXPOSE 80

# Jalankan Apache dengan perintah resmi agar tidak ada error bentrok MPM
CMD ["apachectl", "-D", "FOREGROUND"]
