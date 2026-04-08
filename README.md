# Rodud Truck Ordering System

A production-ready full-stack application built with Laravel (Backend & Admin Panel) and React Native Expo (Mobile App).

## Production Setup Guide

### 1. Backend (Laravel API & Admin Panel)

**Server Requirements:**
- PHP 8.2+ with necessary extensions (OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath)
- MySQL 8.0+ or PostgreSQL
- Composer
- A web server like Nginx or Apache

**Setup Steps:**
```bash
cd backend
composer install --optimize-autoloader --no-dev
cp .env
php artisan key:generate
```

**Configure Environment Variables**
Edit the `.env` file with your production secrets:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-production-domain.com`
- `DB_CONNECTION=mysql`
- `DB_DATABASE=your_database_name`
- `DB_USERNAME=your_database_user`
- `DB_PASSWORD=your_secure_password`
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD` (For admin notifications)
- `SANCTUM_STATEFUL_DOMAINS=your-production-domain.com`

**Run Migrations and Optimizations:**
```bash
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Mobile App (React Native Expo)

**Pre-build configuration:**
1. Open `mobile/services/api.ts`.
2. Update `API_BASE_URL` to point to your live production backend URL (e.g., `https://your-production-domain.com/api`).
3. If using Twilio or real-time tracking in the future, add the respective keys.

**Build for Production:**
1. Ensure your `app.json` has the correct `bundleIdentifier` (iOS) and `package` (Android) names.
2. Run EAS Build:
```bash
cd mobile
npx eas build --platform all
```
*(Requires an Expo account and eas-cli installed: `npm install -g eas-cli`)*
