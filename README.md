# Rodud Truck Ordering System

A full-stack application built with Laravel (Backend & Admin Panel) and React Native Expo (Mobile App).

## Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm
- SQLite (for standard local development)

## Installation Guide

### 1. Setup Backend & Admin Panel (Laravel)
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=AdminSeeder
```

**To start the backend server:**
```bash
php artisan serve
```
Admin Panel will be running at: `http://localhost:8000/admin/login`

### 2. Setup Mobile App (React Native Expo)
```bash
cd mobile
npm install
```

**To start the mobile app:**
```bash
npx expo start
```
Scan the QR code with the Expo Go app or press `a` or `i` to open in Android Emulator / iOS Simulator.

---
**Note:** If you are running the mobile app on a physical device, make sure to update the `API_BASE_URL` in `mobile/services/api.ts` to your machine's local IP address instead of `localhost` or `10.0.2.2`.
