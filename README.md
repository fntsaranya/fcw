# Functional Chronic Wellness (PHP Edition)

This is a full PHP migration of the original FastAPI project for Hostinger shared hosting compatibility.

## Tech Stack

- PHP 8.1+
- PostgreSQL (Neon) or MySQL via PDO
- PHPMailer (SMTP)
- Vanilla HTML/CSS/JS

## Features Preserved

- Public pages: Home, About, Services, Resources
- Register for Enquiry form (`/enquiry`): DB save + SMTP notification
- Paid appointment flow (`/contact`):
  - Booking form
  - UPI payment page with GPay/PhonePe QR support
  - Payment acknowledgement API
  - Payment status API
- Blog system: list, detail, admin CRUD (PIN-protected via DB manager)
- Admin verify endpoint (`POST /admin/verify`)
- Health assessment form (all original questions)
- Assessment scoring + interpretation logic
- Admin dashboard:
  - contact add/edit/delete
  - assessment edit/delete
  - blog add/edit/delete
  - appointment payment verify/reject/delete
  - tabbed data manager
- Health endpoints:
  - `GET /health`
  - `GET /health/db`
- Payment endpoints:
  - `POST /api/payments/acknowledge`
  - `GET /api/payments/status/{booking_reference}`

## Project Structure

- `index.php` - Front controller
- `.htaccess` - Rewrite + protected internal folders
- `config/bootstrap.php` - Session, env load, autoload
- `src/` - Controllers, services, repositories, core classes
- `templates/` - PHP view templates
- `static/` - CSS, JS, images
- `.env.example` - Environment template
- `composer.json` - PHP dependencies

## Local Setup

1. Copy env template:
   ```bash
   cp .env.example .env
   ```
2. Fill `.env` values (database, pin, smtp, appointment payment/UPI settings).
3. Install dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
4. Serve locally with your preferred PHP server:
   ```bash
   php -S localhost:8000
   ```
5. Open `http://localhost:8000`.

## Deployment

See: `HOSTINGER_SHARED_PLAN_DEPLOYMENT.md`

Ready clean upload bundle for Hostinger:

- `fcw_php_hostinger_release_*.zip` (latest version in project root)



