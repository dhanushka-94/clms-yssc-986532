# YSSC Club Management System

A comprehensive club management system built with Laravel for managing members, staff, events, and finances.

## Features

- User Management
- Member Management
- Staff Management
- Event Management
- Attendance Tracking
- Financial Management
- Sponsorship Management
- Report Generation

## Requirements

- PHP >= 8.1
- Composer
- MySQL
- Node.js & NPM

## Installation

1. Clone the repository:
```bash
git clone https://github.com/dhanushka-94/yssc-clms.git
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install NPM dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env` file

7. Run migrations and seed the database:
```bash
php artisan migrate --seed
```

8. Create storage link:
```bash
php artisan storage:link
```

9. Build assets:
```bash
npm run build
```

10. Start the development server:
```bash
php artisan serve
```

## Default Admin Credentials

- Email: admin@example.com
- Password: password

## License

This project is licensed under the MIT License.
