# Guía para Contribuir al Proyecto de la nueva versión de WERKEN

Este archivo te ayudará a entender cómo puedes colaborar de forma correcta y cumplir con las pautas del proyecto.

---

## 1. Introducción y Agradecimientos

Bienvenido(a) al desarrollo de la nueva versión de WERKEN. Este proyecto tiene como objetivo optimizar la gestión y búsqueda de recursos académicos para mejorar la experiencia de los usuarios de la plataforma. 

---

## 2. Cómo Contribuir

### 2.1 Flujo de Trabajo Estándar
1. **Realiza un "fork" del repositorio**.
2. **Crea una nueva rama** para los cambios que deseas realizar. Usa un nombre descriptivo como `feat/nueva-funcionalidad` o `fix/correccion-error`.
3. **Desarrolla y prueba los cambios** en tu entorno local.
4. **Envía un pull request (PR)** al repositorio principal, detallando:
   - Qué cambios realizaste.
   - Por qué son necesarios.
   - Qué problema resuelven o qué funcionalidad añaden.

---

## 3. Estándares de Estilo y Formato de Código

Sigue los estándares de estilo **PSR-12** para PHP. Además:
- Nombra variables, métodos y funciones **en español**, en la medida de lo posible.
- Documenta el código con comentarios claros y concisos.
- Usa herramientas como **PHP-CS-Fixer** para verificar que el código cumple con los estándares.

---

## 4. Configuración del Entorno de Desarrollo

Para configurar el entorno de desarrollo, sigue estos pasos:

1. Asegúrate de tener instalados:
   - Laravel (PHP 7.4).
   - Composer.
   - Apache.
   - SQL Server.

2. Clona el repositorio e instala las dependencias:
   ```bash
   git clone https://github.com/Lucks21/TallerDeDesarrollo.git
   cd werken
   composer install
   ```

3. Configura el archivo `.env`:
    Base de datos: **SQL Server**.
    Variables de entorno requeridas: verifica los ejemplos en `.env.example`.

4. Corre las migraciones para configurar la base de datos:
    ```bash
    php artisan migrate
    ```

---

## 5. Pruebas de Código

Antes de enviar tus cambios, asegúrate de que todo funcione correctamente ejecutando las pruebas unitarias. Utilizamos PHPUnit para las pruebas en Laravel.

Para ejecutar las pruebas:
    ```bash
    php artisan test
    ```

Verifica que todas las pruebas pasen antes de abrir un pull request.

---

## 6. Cómo Enviar un Pull Request

Sigue estos pasos al enviar un PR:

1. Verifica que:
    - Todas las pruebas pasen.
    - El código cumple con los estándares de estilo y está correctamente documentado.

2. Especifica la rama de destino del pull request (por ejemplo, `main` o `develop`).

3. Incluye una descripción detallada que explique:
    - Qué cambios realizaste.
    - Por qué son necesarios.
    - Cualquier otra información relevante.

**GRACIAS POR TU APORTE**


