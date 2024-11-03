# Proyecto: Nueva versión del sistema WERKEN
Este proyecto tiene como objetivo actualizar el sistema de bibliotecas en línea de la Universidad del Bío-Bío, conocido como WERKEN, con el propósito de mejorar la búsqueda de recursos académicos y optimizar la experiencia de usuario. Esta actualización proporcionará una plataforma más accesible y eficiente, facilitando el acceso y gestión de información académica para apoyar el aprendizaje y la investigación en la comunidad universitaria.
## Requisitos
Para ejecutar este proyecto, necesitas tener instalado en tu sistema:
- **Docker**: Sigue las instrucciones a continuación para instalarlo en Windows.
- **Acceso a Internet**: Para descargar Docker Desktop y otros recursos.
## Instalación de Docker en Windows
1. **Descargar Docker Desktop**: Ve a [Docker Desktop para Windows](https://desktop.docker.com/) y descarga la versión para Windows.
2. **Instalar Docker Desktop**: Ejecuta el archivo descargado y sigue las instrucciones en pantalla.
3. **Verificar la instalación**: Una vez instalado, abre una terminal de PowerShell y ejecuta el comando `docker --version` para confirmar que Docker está correctamente instalado.
## Preparación del Proyecto
1. **Clonar el repositorio**:
- Abre PowerShell y navega al directorio donde deseas clonar el proyecto.
- Clona el repositorio usando:
```bash
git clone <URL_DEL_REPOSITORIO>
```
- Ingresa al directorio del proyecto:
```bash
cd <NOMBRE_DEL_DIRECTORIO>
```
2. **Verificar el Dockerfile**:
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
2. **Eliminar el Contenedor**:
```bash
docker rm mi_contenedor_php
3. **Eliminar la Imagen**:
```bash
docker rmi mi_imagen_php
