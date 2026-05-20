# E-Commerce Starter Kit

A production-ready Laravel e-commerce platform built for Nigerian businesses. Accept payments via Paystack, manage products and orders from a sleek admin dashboard, and launch your online store in minutes.

![Version](https://img.shields.io/badge/version-1.0.0-green) ![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue) ![Laravel](https://img.shields.io/badge/Laravel-10-orange) ![Paystack](https://img.shields.io/badge/Paystack-Integration-blue)

## Features

- **Paystack Payment Integration** — Accept Nigerian Naira (NGN) payments via card, bank transfer, USSD, and mobile money
- **Admin Dashboard** — Full product and order management with real-time stats
- **Product Management** — Add, edit, toggle availability, featured products
- **Order Tracking** — Status history, customer details, payment verification
- **Shopping Cart** — Session-based cart, quantity management
- **Digital Products** — Instant download links after payment
- **Email Notifications** — Purchase confirmation with download link
- **SEO Ready** — Meta fields for every product
- **Mobile Responsive** — Works on all devices
- **Nigerian Market Ready** — NGN currency, Nigerian pricing, local payment methods

## Quick Start

### Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.1+ |
| MySQL | 5.7+ or MariaDB 10.3+ |
| Composer | 2.x |
| SSL | Required for Paystack |

### Installation

```bash
# 1. Extract the zip file to your web root
cd /var/www/myestore

# 2. Install PHP dependencies
composer install

# 3. Create MySQL database
mysql -u root -p
CREATE DATABASE ecom_starter_kit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Run database migrations
php artisan migrate

# 7. Seed sample data
php artisan db:seed

# 8. Set storage permissions
chmod -R 775 storage bootstrap/cache
```

### Web Server Configuration

**Apache** — Ensure `public/.htaccess` is active (already included):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^$ public [L]
    RewriteRule .* public/$0 [L]
</IfModule>
```

**Nginx:**

```nginx
location / {
    try_files $uri $uri/ /public/index.php?$query_string;
}
```

Point your domain/web root to the `public/` folder.

## Configuration

### Environment Variables (.env)

```env
APP_NAME="My E-Shop"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecom_starter_kit
DB_USERNAME=root
DB_PASSWORD=your_password

# Paystack (get from https://dashboard.paystack.co/settings/api)
PAYSTACK_PUBLIC_KEY=pk_live_YOUR_PUBLIC_KEY
PAYSTACK_SECRET_KEY=sk_live_YOUR_SECRET_KEY

# Mail (SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@yourdomain.com
MAIL_FROM_NAME="Your Store"
```

### Paystack Setup

1. Sign up at [dashboard.paystack.co](https://dashboard.paystack.co)
2. Go to **Settings → API Keys**
3. Copy **Public Key** → `PAYSTACK_PUBLIC_KEY` in `.env`
4. Copy **Secret Key** → `PAYSTACK_SECRET_KEY` in `.env`
5. Set **Callback URL** in Paystack dashboard to `https://yourdomain.com/checkout/success`
6. For testing, use `pk_test_` and `sk_test_` keys first

## Admin Panel

**URL:** `https://yourdomain.com/admin/login`

| Credential | Value |
|-----------|-------|
| Email | `admin@estore.com` |
| Password | `admin123` |

**Important:** Change the admin password immediately after first login. Edit the `admin_users` table or use the seeder to reset.

### Admin Features

- **Dashboard** — Total orders, today's orders, revenue, pending orders, recent orders, top products
- **Products** — Create, edit, delete, toggle active/featured, search, filter by category
- **Orders** — View details, update payment status, track status history

## File Structure

```
├── app/
│   ├── Console/Commands/
│   ├── Exceptions/Handler.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin controllers
│   │   │   └── Front/          # Public storefront controllers
│   │   ├── Middleware/         # Auth, session, security
│   │   └── Requests/           # Form validation
│   ├── Models/                 # Eloquent models
│   ├── Providers/              # Service providers
│   └── Services/
│       └── PaystackService.php # Payment integration
├── bootstrap/app.php
├── config/                     # All Laravel config files
├── database/
│   ├── migrations/             # 9 migration files
│   └── seeders/                # Products, admin, settings
├── public/
│   ├── index.php              # Application entry point
│   └── .htaccess              # Apache rewrite rules
├── resources/views/
│   ├── admin/                  # Admin panel views
│   ├── emails/                # Email templates
│   ├── front/                 # Storefront views
│   └── layouts/               # Master layouts
├── routes/
│   ├── web.php                # All web routes
│   ├── api.php                # API routes
│   └── channels.php           # Broadcast channels
├── storage/                    # Logs, cache, uploads
├── tests/
│   ├── Feature/               # Integration tests
│   └── Unit/                  # Unit tests
├── composer.json
├── .env.example
├── .gitignore
└── README.md
```

## Pages

| Route | Page |
|-------|------|
| `/` | Homepage with featured products |
| `/shop` | Product listing with search/filter/sort |
| `/product/{slug}` | Product detail page |
| `/cart` | Shopping cart |
| `/checkout` | Checkout with Paystack payment |
| `/checkout/success` | Payment success + download |
| `/admin/login` | Admin login |
| `/admin` | Admin dashboard |
| `/admin/products` | Product management |
| `/admin/orders` | Order management |

## Customization

### Change Store Name

Edit `.env`:
```env
APP_NAME="Your Store Name"
```

### Add Products

1. Login to admin panel
2. Go to **Products → Add New**
3. Fill in name, slug, price, description, image URL
4. Save

### Modify Emails

Email templates are in `resources/views/emails/`. Edit `order-purchase.blade.php` to customize the purchase confirmation email.

### Add New Payment Methods

The `app/Services/PaystackService.php` handles all Paystack interactions. To add Stripe or Flutterwave:

1. Create a new service class (e.g., `FlutterwaveService.php`)
2. Register it in `config/app.php` providers
3. Update `CheckoutController` to use the new service

## Troubleshooting

### Blank White Screen

```bash
php artisan cache:clear
php artisan config:clear
chmod -R 775 storage bootstrap/cache
```

### Database Connection Error

- Verify MySQL is running: `systemctl status mysql`
- Check `.env` credentials
- Ensure database exists: `mysql -u root -p -e "SHOW DATABASES;"`

### Payment Not Redirecting

- Verify Paystack API keys are correct (not test keys in production)
- Ensure callback URL is accessible: `https://yourdomain.com/checkout/success`
- Check server can reach Paystack API: `curl -I https://api.paystack.co`

### Admin Login Failing

```bash
php artisan tinker
>>> App\Models\AdminUser::find(1)->toArray();
```
If no admin exists:
```bash
php artisan db:seed --class=AdminUserSeeder
```

### Session Issues

```env
SESSION_DRIVER=database
```
Or use Redis:
```env
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## License

MIT License — free to use for personal and commercial projects.

---

Built for Nigerian entrepreneurs. Launch faster with [JoAla Digital](https://joala.com.ng).