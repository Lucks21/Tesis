#imagen base de PHP con Apache en Debian Bullseye de Docker Hub
FROM php:7.4.33-apache-bullseye
#Esto es para actualizar los paquetes e instalar dependencias
#zip: para comprimir y descomprimir archivos
#git: para clonar repositorios
#curl: para descargar archivos y verificar conexiones
#gnupg: herramienta necesaria para manejar la importación de la clave del repositorio de Microsoft
#unixodbc y unixodbc-dev: son necesarios para conectar PHP a la base de datos de microsoft SQL server
RUN apt update && apt install -y \
    zip \  
    git \
    curl \
    gnupg \
    unixodbc \
    unixodbc-dev

#se configura e instala el soporte ODBC en PHP para que se pueda conectar a la base de datos de SQL Server
RUN docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr \ 
    && docker-php-ext-install pdo_odbc

# se agrega el repositorio de Microsoft y se instala el controlador ODBC para SQL Server
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list && \
    apt-get update && \
    ACCEPT_EULA=Y DEBIAN_FRONTEND=noninteractive apt-get install -y msodbcsql18 mssql-tools && \
    echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bash_profile && \
    echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc && \
    sed -i -E 's/(CipherString\s*=\s*DEFAULT@SECLEVEL=)2/\11/' /etc/ssl/openssl.cnf

WORKDIR /var/www/html
#para tener permisos de lectura y ejecución
RUN chmod -R 755 /var/www/html 

# configuración de Apache para permitir el acceso al directorio de trabajo
RUN echo "<Directory \"/var/www/html\">\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/custom-permissions.conf && \
    a2enconf custom-permissions && \
    a2enmod rewrite

#se copia Composer(administrador de dependencias para PHP) desde la imagen oficial para tenerlo disponible en el contenedor
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

#se expone el puerto 80 para acceder al servidor Apache desde el exterior del contenedor
EXPOSE 80

#se configura Apache para ejecutarse en primer plano
ENTRYPOINT ["/usr/sbin/apache2ctl"]
CMD ["-D", "FOREGROUND"]
