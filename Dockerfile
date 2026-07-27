FROM php:8.2-cli

# Install ekstensi MySQL untuk PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory ke folder aplikasi
WORKDIR /var/www/html

# Copy semua file kodingan kamu
COPY . .

# Jalankan PHP Built-in Server langsung mendengarkan $PORT dari Railway
CMD php -S 0.0.0.0:${PORT:-8080}
