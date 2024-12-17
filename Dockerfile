# Imagen base de PHP con Apache en Debian Bullseye
FROM php:7.4.33-apache-bullseye

# Instalar dependencias del sistema
RUN apt update && apt install -y \
    zip \
    git \
    curl \
    gnupg \
    unixodbc \
    unixodbc-dev \
    && apt-get clean

# Configurar e instalar el soporte ODBC en PHP para SQL Server
RUN docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr && \
    docker-php-ext-install pdo_odbc

# Agregar el repositorio de Microsoft y el controlador ODBC para SQL Server 2017
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl https://packages.microsoft.com/config/debian/10/prod.list > /etc/apt/sources.list.d/mssql-release.list && \
    apt-get update && \
    ACCEPT_EULA=Y DEBIAN_FRONTEND=noninteractive apt-get install -y msodbcsql17=17.10.1.1-1 mssql-tools=17.10.1.1-1 && \
    echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bash_profile && \
    echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc && \
    sed -i -E 's/(CipherString\s*=\s*DEFAULT@SECLEVEL=)2/\11/' /etc/ssl/openssl.cnf

# Instalar Node.js y npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt install -y nodejs && \
    apt-get clean

# Configurar Apache para servir el directorio 'werken/public'
WORKDIR /var/www/html/werken/public

RUN echo "<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/werken/public\n\
    <Directory /var/www/html/werken/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

# Copiar los archivos del proyecto
COPY . /var/www/html

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Instalar dependencias de Composer y npm dentro de la carpeta 'werken'
WORKDIR /var/www/html/werken
RUN composer install --no-interaction --no-dev --optimize-autoloader && \
    npm install && \
    npm run dev

# Asegurar permisos adecuados
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
# Generar la clave de aplicación de Laravel
RUN php artisan key:generate
# Exponer el puerto 80
EXPOSE 80

# Configurar Apache para ejecutarse en primer plano
ENTRYPOINT ["/usr/sbin/apache2ctl"]
CMD ["-D", "FOREGROUND"]
