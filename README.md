# Proyecto: Nueva versión del sistema WERKEN
Este proyecto tiene como objetivo actualizar el sistema de bibliotecas en línea de la Universidad del Bío-Bío, conocido como WERKEN, con el propósito de mejorar la búsqueda de recursos académicos y optimizar la experiencia de usuario. Esta actualización proporcionará una plataforma más accesible y eficiente, facilitando el acceso y gestión de información académica para apoyar el aprendizaje y la investigación en la comunidad universitaria.
## Tecnologías y Versiones Utilizadas
Este proyecto utiliza las siguientes tecnologías y herramientas:
- **Docker**: 27.3.1
- **PHP**: 7.4.1
- **Composer**: 2.8.1
- **Laravel**: 8.83.28
- **SQL Server**: Microsoft SQL Server 2017 versión 14.0.3445.2
- **Apache**: 2.4.38
- **Node.js**: 20.18.0
- **Npm**: 10.8.2
## Requisitos
Para ejecutar este proyecto, necesitas tener instalado en tu sistema:
- **Docker**: Sigue las instrucciones a continuación para instalarlo en tu sistema operativo.
- **Acceso a Internet**: Para descargar Docker Desktop y otros recursos.
## Instalación de Docker
1. **Descargar Docker Desktop**: Descargar la version de `Docker Desktop` que corresponda a tu sistema operativo: [Docker Desktop para Windows](https://docs.docker.com/desktop/install/windows-install/), [Docker Desktop para Linux](https://docs.docker.com/desktop/install/linux/) o [Docker Desktop para Mac](https://docs.docker.com/desktop/install/mac-install/).
2. **Node.js**: sigue los pasos que correspondan para tu sistema operativo [Instalar Node.js](https://nodejs.org/).
3. **Instalar Docker Desktop**: Sigue los pasos que correspondan para tu sistema operativo.
4. **Verificar la instalación**: Para verificar que Docker Desktop esta correctamente instalado, abre una terminal y ejecuta el comando 
```bash
docker --version
```
## Preparación del Proyecto
1. **Clonar el repositorio**:
- Abre una terminal y navega al directorio donde deseas clonar el proyecto.
- Clona el repositorio usando:
```bash
git clone <URL_DEL_REPOSITORIO>
```
- Ingresa al directorio del proyecto:
```bash
cd <NOMBRE_DEL_DIRECTORIO>
```
2. **Crear el archivo .env**:
- Crea una copia del archivo de configuración .env a partir de .env.example
```bash
cp .env.example .env
```
3. **Configurar el archivo .env**:
- Edita el archivo .env con los datos correctos de la base de datos y otros ajustes
```dotenv
DB_CONNECTION=sqlsrv
DB_HOST=127.0.0.1          # O el host donde está tu base de datos
DB_PORT=1433               # Puerto por defecto de SQL Server
DB_DATABASE=nombre_bd      # Nombre de tu base de datos
DB_USERNAME=usuario        # Usuario de la base de datos
DB_PASSWORD=contraseña     # Contraseña de la base de datos
```
4. **Verificar el Dockerfile**:
- Asegúrate de que el archivo `Dockerfile` está en el directorio raíz del proyecto. Este archivo contiene todas las instrucciones necesarias para construir la imagen Docker.
## Construcción de la Imagen Docker
1. **Construir la imagen Docker**:
- Desde la terminal en el directorio del proyecto, ejecuta el siguiente comando:
```bash
docker build -t mi_imagen .
```
- Esto construirá la imagen a partir del `Dockerfile` que ya está incluido en el repositorio. La opción `-t` etiqueta la imagen como `mi_imagen`.
## Ejecución del Contenedor
1. **Ejecutar el contenedor**:
- Inicia el contenedor con el siguiente comando:
```bash
docker run -d -p 8080:80 --name mi_contenedor mi_imagen
```
- Este comando hace lo siguiente:
- `-d` ejecuta el contenedor en segundo plano.
- `-p 8080:80` redirige el puerto 8080 de tu computadora al puerto 80 del contenedor.
- `--name mi_contenedor` asigna el nombre `mi_contenedor` al contenedor.
2. **Acceder al Proyecto**:
- Abre tu navegador y ve a `http://localhost:8080`. Si todo ha funcionado correctamente, deberías ver la página inicial del servidor Apache.
## Finalización del Proyecto
Para detener y eliminar el contenedor, usa los siguientes comandos:
1. **Detener el Contenedor**:
```bash
docker stop mi_contenedor_php
```
2. **Eliminar el Contenedor**:
```bash
docker rm mi_contenedor_php
```
3. **Eliminar la Imagen**:
```bash
docker rmi mi_imagen_php
```
## Problemas comunes y soluciones:
1. **Docker no encuentra el Dockerfile**:
- Asegúrate de estar en el directorio raíz del proyecto al ejecutar el docker build.
2. **Puerto en uso**:
- Cambia el puerto al ejecuta el contenedor:
```bash
docker run -d -p 9090:80 --name mi_contenedor mi_imagen
```