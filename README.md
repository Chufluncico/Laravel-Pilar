# Proyecto Laravel

## Requisitos

* PHP >= 8.x
* Composer
* Node.js

## Instalación

```bash
git clone <repo>
cd proyecto

composer install
cp .env.example .env
php artisan key:generate

npm install
npm run build

php artisan migrate
```

## Ejecutar

```bash
php artisan serve
```
