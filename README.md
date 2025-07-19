# Teknologi Integrasi Armada - Employee Management System

## Frontend

```bash
yarn install
```

## Backend

```bash
composer install
composer update
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate:fresh --seed
```

## Node.js

```bash
yarn install
yarn run dev
yarn run build
```

## API - Must Login

- departments
- employees
- attendances
- attendance/histories
- attendance/by-department
