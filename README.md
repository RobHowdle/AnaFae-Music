<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## AnaFaeMusic

This is the Laravel + Vue 3 website for Ana Fae Music.

### Local development

- Run everything (PHP server + Vite): `composer run dev`
- Or separately:
    - Backend: `php artisan serve`
    - Frontend: `npm run dev`

### Coming soon / maintenance

- The public homepage (`/`) renders the Coming Soon page: [routes/web.php](routes/web.php)
- The Vue component is: [resources/js/ComingSoon.vue](resources/js/ComingSoon.vue)
- The Blade view is: [resources/views/coming-soon.blade.php](resources/views/coming-soon.blade.php)
- The maintenance (503) view is: [resources/views/errors/503.blade.php](resources/views/errors/503.blade.php)

### Admin login

- Login: `/login`
- Admin entry point (redirects to dashboard): `/admin`
- Public self-registration is disabled (no `/register` route)

To create the first user locally:

- `php artisan tinker`
- `\App\Models\User::create(['name' => 'Admin', 'email' => 'you@example.com', 'password' => bcrypt('change-me')]);`

### Docker

Build and run the site in Docker (Nginx + PHP-FPM in one container):

- Start: `docker compose up --build`
- Visit: `http://localhost:8000`

Notes:

- `storage/` is persisted via a named volume (the SQLite DB file lives in `storage/database.sqlite`).
- Auto-migrations run on container start (toggle with `AUTO_MIGRATE=0`).

### Deploying with Laravel Forge (Hetzner)

Forge’s default deployment model is to run Laravel directly on the server (Nginx + PHP-FPM on the host). That’s the simplest approach here; you can keep Docker for local/dev.

Recommended Forge deploy script steps:

- `composer install --no-interaction --prefer-dist --optimize-autoloader`
- `php artisan migrate --force`
- `npm ci && npm run build`
- `php artisan config:cache && php artisan route:cache && php artisan view:cache`

If you want a ready-to-paste script, see: [deploy/forge-deploy.sh](deploy/forge-deploy.sh)

Production env reminders:

- Set `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL=https://your-domain`
- Ensure `storage/` and `bootstrap/cache/` are writable by the web user

To put production into maintenance mode:

- Down: `php artisan down`
- Up: `php artisan up`

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
