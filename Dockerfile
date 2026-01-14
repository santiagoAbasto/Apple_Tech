# ✅ Imagen base con Apache y PHP 8.2
FROM php:8.2-apache

# ✅ Timezone del sistema (Bolivia)
ENV TZ=America/La_Paz

# ✅ Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    tzdata \
    zip unzip git curl libpq-dev libzip-dev libpng-dev libonig-dev \
    && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone \
    && docker-php-ext-install pdo pdo_pgsql zip gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ✅ Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ✅ Directorio de trabajo
WORKDIR /var/www/html

# ✅ Habilitar mod_rewrite
RUN a2enmod rewrite

# ✅ Cambiar DocumentRoot a /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf
