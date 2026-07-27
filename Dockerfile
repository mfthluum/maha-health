FROM php:8.2-apache

# Install ekstensi MySQL untuk PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Copy seluruh kodingan ke folder web server
COPY . /var/www/html/

# Ubah konfigurasi port Apache agar mengikuti variabel PORT dari Railway (default 8080/80)
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Buka port
EXPOSE 80

# Jalankan Apache
CMD ["apachectl", "-D", "FOREGROUND"]
