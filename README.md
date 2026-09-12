<p align="center"><a href="#" target="_blank"><img src="/public/img/logo-extendido.png" width="400" alt="PLanzarote"></a></p>

## PLanzarote

**PLanzarote** es una plataforma web diseñada para facilitar la creación, descubrimiento y participación en planes y actividades en Lanzarote. Los usuarios pueden crear sus propios planes, consultar actividades disponibles y apuntarse a aquellas que sean de su interés.

**PLanzarote** está planteada como un espacio digital para facilitar la organización de actividades y la conexión entre personas que comparten intereses. La plataforma permite a los usuarios proponer sus propios planes, descubrir nuevas actividades y encontrar personas interesadas en participar en ellas.

Además, **PLanzarote** busca cubrir una necesidad dentro de la comunidad de Lanzarote, proporcionando un punto de encuentro digital para organizar actividades de ocio, deporte, excursiones, reuniones y cualquier otro tipo de plan.

## ¿Qué pueden hacer los usuarios de PLanzarote?

En **PLanzarote**, los usuarios pueden crear y gestionar sus propios planes indicando información como el nombre, descripción, ubicación, fecha, hora e imagen de la actividad.

Además, pueden consultar los planes publicados por otros usuarios y utilizar el buscador para encontrar actividades por nombre, descripción o ubicación.

Los usuarios también pueden apuntarse a los planes que les interesen y abandonar posteriormente aquellos en los que participen. Los creadores de cada plan pueden consultar las personas que se hayan apuntado a sus actividades.

La plataforma cuenta también con un sistema de gestión de planes que permite modificarlos o eliminarlos, así como un sistema automático encargado de eliminar los planes cuya fecha ya haya pasado.

## Instalación

Para ejecutar **PLanzarote** localmente, sigue estos pasos:

### Requisitos previos:

- PHP >= 8.2 , y todas las extensiones necesarias:
```
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php php-cli php-mbstring php-xml php-bcmath php-curl php-zip unzip curl -y
```
Confirma la instalación de PHP:
```
php -v
```
- Composer
```
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```
Verifica la instalación:
```
composer --version
```
- MySQL
```
sudo apt install mysql-server php-mysql -y
```
Configura la base de datos y el usuario correspondiente:
```
sudo mysql
CREATE DATABASE planzarote;
CREATE USER 'planzarote'@'localhost' IDENTIFIED BY 'planzarote';
GRANT ALL PRIVILEGES ON *.* TO 'planzarote'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```
- Node.js
```
sudo apt install nodejs npm
```
Confirma la instalación:
```
node -v
npm -v
```
- Git
```
sudo apt install git
```
Confirma la instalación:
```
git --version
```

1. Clona el repositorio:
```
git clone https://github.com/tomasvillani/planzarote.git
```
2. Accede a la carpeta:
```
cd planzarote
```
3. Otorga los permisos correspondientes:
```
sudo chmod 777 -R ./*
```
4. Instala las dependencias de Composer y de Node.js:
```
composer install
npm install
npm run build
```
5. Copia el archivo .env.example a un archivo .env.
6. Modifica estas líneas del .env:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=planzarote
DB_USERNAME=planzarote
DB_PASSWORD=planzarote
```
7. Genera la clave de encriptación:
```
php artisan key:generate
```
8. Ejecuta las migraciones:
```
php artisan migrate
```
9. Para poder almacenar las imágenes para las cartas, ejecuta el siguiente comando:
```
php artisan storage:link
```
10. Inicia el servicio:
```
php artisan serve
```

De esta manera, si accedes por 127.0.0.1:8000, la página debe aparecer sin problema.

### Tareas programadas

**PLanzarote** incluye una tarea programada encargada de eliminar automáticamente los planes cuya fecha ya haya pasado.

El comando utilizado para realizar esta operación es:
```
php artisan plans:delete-expired
```

El planificador de Laravel ejecuta este comando periódicamente para mantener la aplicación actualizada y evitar que permanezcan planes caducados.

Para ejecutar el planificador durante el desarrollo se puede utilizar:
```
php artisan schedule:work
```

## Documentos de interés

Consulta los siguientes documentos para obtener información detallada sobre el proceso de desarrollo:

- [Documento de análisis](https://drive.google.com/file/d/1D7ULzqb4AtqkEVHwsxR8ogC-E8pXDdbb/view?usp=sharing)
- [Documento de diseño](https://drive.google.com/file/d/1kz90s_v_uX1hI27YHdHZfT7oKyFxMaCa/view?usp=sharing)

## Visita el Proyecto Online

Puedes visitar mi página web [aquí](https://planzarote.alwaysdata.net/)

## Vídeo de Youtube

Puedes ver el vídeo del proyecto en Youtube [aquí](https://youtu.be/EuI-VvmQIAs?si=j_X0VAPsXDQnODFg)
