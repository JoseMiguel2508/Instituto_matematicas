# Instituto Matemáticas

Sistema integral de gestión académica y financiera para el Instituto de Matemáticas, desarrollado en Laravel.

## Requisitos Previos

- PHP 8.1 o superior
- Composer
- Node.js y npm
- Servidor MySQL o MariaDB

## Guía de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/JoseMiguel2508/Instituto_matematicas.git
   cd Instituto_matematicas
   ```

2. **Instalar dependencias de PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias de Frontend**
   ```bash
   npm install
   npm run build
   ```

4. **Configurar el entorno**
   Copia el archivo de ejemplo para crear tu configuración local:
   ```bash
   cp .env.example .env
   ```
   Abre el archivo `.env` y configura las credenciales de tu base de datos:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=InstitutoMatematicas
   DB_USERNAME=tu_usuario
   DB_PASSWORD=tu_contraseña
   ```

5. **Generar la Key de la Aplicación**
   ```bash
   php artisan key:generate
   ```

6. **Base de Datos**
   - Crea una base de datos vacía en MySQL llamada `InstitutoMatematicas` (o el nombre que hayas puesto en `.env`).
   - Importa el esquema estructural oficial que se encuentra en la raíz del proyecto:
     ```bash
     mysql -u tu_usuario -p InstitutoMatematicas < esquema_bd.sql
     ```
   *(Nota: Este proyecto no utiliza migraciones estándar de Laravel, todo el esquema y relaciones están en el archivo `esquema_bd.sql`)*.

7. **Crear enlaces de almacenamiento (Opcional si usas subida de archivos)**
   ```bash
   php artisan storage:link
   ```

8. **Levantar el servidor local**
   ```bash
   php artisan serve
   ```
   El sistema estará disponible en `http://localhost:8000`.

## Accesos por defecto (Roles de Prueba)

Al no tener migraciones ni seeders de Laravel estándar, si la base de datos importada no tiene usuarios, deberás registrar uno directamente en la tabla `usuario` o utilizar los scripts de generación (si están incluidos en algún backup de datos). 

Si estás usando el volcado con datos, los accesos principales suelen ser:
- **Admin**: admin / password123 (o lo que se haya configurado).