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
  - Razorpay Checkout payment page
  - Server-side Razorpay order creation
  - Checkout signature verification
  - Webhook reconciliation for payment status
  - Payment status API
- Blog system: list, detail, admin CRUD (PIN-protected via DB manager)
- Admin verify endpoint (`POST /admin/verify`)
- Health assessment form (all original questions)
- Assessment scoring + interpretation logic
- Admin dashboard:
  - contact add/edit/delete
  - assessment edit/delete
  - blog add/edit/delete
  - Razorpay appointment payment tracking/delete
  - tabbed data manager
- Health endpoints:
  - `GET /health`
  - `GET /health/db`
- Payment endpoints:
  - `POST /api/payments/order`
  - `POST /api/payments/verify`
  - `POST /api/payments/failure`
  - `POST /api/payments/razorpay/webhook`
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
2. Fill `.env` values (database, pin, smtp, appointment fee, and Razorpay settings).
   For local testing without MySQL/PostgreSQL credentials, use:
   ```bash
   DATABASE_URL=sqlite://storage/fcw-local.sqlite
   ```
   Razorpay Checkout requires these values from the Razorpay dashboard:
   ```bash
   RAZORPAY_KEY_ID=rzp_test_xxxxxxxxxxxxxx
   RAZORPAY_KEY_SECRET=your-razorpay-key-secret
   RAZORPAY_WEBHOOK_SECRET=your-razorpay-webhook-secret
   ```
3. Install dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
4. Serve locally with your preferred PHP server:
   ```bash
   php -S localhost:8000
   ```
5. Open `http://localhost:8000`.

## Razorpay Setup

Create payments in Razorpay Test Mode while developing. Configure the webhook URL in Razorpay as:

```text
https://YOUR_DOMAIN/api/payments/razorpay/webhook
```

Recommended webhook events: `payment.authorized`, `payment.captured`, and `payment.failed`. Use the same webhook secret in Razorpay and `RAZORPAY_WEBHOOK_SECRET`.

If local Windows PHP shows a cURL error like `self-signed certificate in certificate chain`, download a CA bundle to `storage/cacert.pem` and keep:

```bash
RAZORPAY_CURL_CAINFO=storage/cacert.pem
```

## Deployment

See: `HOSTINGER_SHARED_PLAN_DEPLOYMENT.md`

Ready clean upload bundle for Hostinger:

- `fcw_php_hostinger_release_*.zip` (latest version in project root)



