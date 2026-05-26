This means even the HIBP API never sees your actual password or full hash.

## ✨ Features

- Check passwords against 900M+ breached passwords
- k-Anonymity model — plaintext passwords never leave your server
- Rate limiting — 10 requests per minute per IP
- Audit logging — every check is logged (never the password itself)
- Terminal-style UI
- REST API with JSON responses
- Docker ready — runs with two commands

## 🚀 Quick Start (Docker)

```bash
git clone https://github.com/gaborati/breach-detector.git
cd breach-detector
cp .env.example .env
docker-compose up --build
docker exec breach-detector-app php artisan migrate
```

Open [http://localhost:8080](http://localhost:8080)

## ⚙️ Manual Setup

Requirements: PHP 8.2+, Composer, MySQL

```bash
git clone https://github.com/gaborati/breach-detector.git
cd breach-detector
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## 📡 API

**POST** `/api/check-password`

```json
{
    "password": "your-password"
}
```

Response:

```json
{
    "breached": true,
    "count": 2254650,
    "message": "This password has been exposed 2254650 times!"
}
```

Rate limit: 10 requests per minute per IP.

## 🔒 Security Notes

- Passwords are never stored or logged
- k-anonymity ensures only a 5-char SHA1 prefix is sent to the HIBP API
- Replace default passwords before any production use

## 🛠️ Tech Stack

- **Backend**: PHP 8.2, Laravel 10
- **Database**: MySQL 8
- **API**: Have I Been Pwned
- **Frontend**: Vanilla JS, HTML/CSS
- **DevOps**: Docker, Nginx
