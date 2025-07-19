Frontend
yarn install

Backend
composer install
composer update
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate:fresh --seed

API - Must Login
departments
employees
attendances
attendance/histories
attendance/by-department
