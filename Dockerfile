# Imagen base de PHP con Apache en Debian Bullseye
FROM php:7.4.1-apache-buster

# Instalar dependencias del sistema con versiones específicas
RUN apt update && apt install -y \
    zip \
    git \
    curl \
    gnupg \
    unixodbc \
    unixodbc-dev \
    nodejs=20.18.0~dfsg-1~deb10u1 \
    npm=10.8.2~dfsg-1~deb10u1

# Configurar e instalar el soporte ODBC en PHP para SQL Server
RUN docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr \
    && docker-php-ext-install pdo_odbc

# Agregar el repositorio de Microsoft y el controlador ODBC para SQL Server 2017
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl https://packages.microsoft.com/config/debian/10/prod.list > /etc/apt/sources.list.d/mssql-release.list && \
    apt-get update && \
    ACCEPT_EULA=Y DEBIAN_FRONTEND=noninteractive apt-get install -y msodbcsql17=17.10.1.1-1 mssql-tools=17.10.1.1-1 && \
    echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bash_profile && \
    echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc && \
    sed -i -E 's/(CipherString\s*=\s*DEFAULT@SECLEVEL=)2/\11/' /etc/ssl/openssl.cnf

# Configuración de Apache para permitir el acceso al directorio de trabajo
WORKDIR /var/www/html
RUN chmod -R 755 /var/www/html

RUN echo "<Directory \"/var/www/html\">\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/custom-permissions.conf && \
    a2enconf custom-permissions && \
    a2enmod rewrite

# Instalar Composer versión 2.8.1
COPY --from=composer:2.8.1 /usr/bin/composer /usr/local/bin/composer

# Copiar los archivos del proyecto al contenedor
COPY . /var/www/html

# Instalar dependencias de Composer y npm
RUN composer install --no-interaction --no-dev --optimize-autoloader && \
    npm install && \
    npm run dev

# Exponer el puerto 80
EXPOSE 80

# Configurar Apache para ejecutarse en primer plano
ENTRYPOINT ["/usr/sbin/apache2ctl"]
CMD ["-D", "FOREGROUND"]
