# ---- Web Dinamis (Feane) : PHP 8.2 + Apache ----
FROM php:8.2-apache

# Ekstensi PDO MySQL agar bisa konek ke MariaDB
RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# Salin seluruh source aplikasi ke document root Apache
COPY . /var/www/html/

# Pastikan folder upload gambar ada & writable oleh Apache
RUN mkdir -p /var/www/html/images/menu \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80
