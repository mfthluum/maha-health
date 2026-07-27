FROM php:8.2-apache

# Matikan MPM event/worker bawaan biar gak bentrok sama prefork
RUN a2dismod mpm_event mpm_worker || true && a2enmod mpm_prefork

# Install ekstensi MySQL untuk PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Copy seluruh kodingan ke folder web server
COPY . /var/www/html/

# Expose port
EXPOSE 80

# Jalankan entrypoint resmi Apache
CMD ["apache2-foreground"]
