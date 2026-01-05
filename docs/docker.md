# Local Development (Docker)

This project includes a complete Docker setup to run the Laravel application locally with:

- PHP 8.4 + Apache
- MySQL
- Redis (cache + queues)
- Mailpit (email testing)
- Bun + Vite (frontend assets & HMR)

You do **not** need PHP, MySQL, or Node installed on your machine.
Only Docker is required.

---

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)

Verify Docker is installed:

```bash
docker --version
docker compose version
```

---

## Files Involved

At the root of the project:

- `Dockerfile` → defines the PHP environment
- `docker-compose.yml` → runs all services together
- `.env` → Laravel configuration (database, mail, etc.)

---

## First-time Setup

Run these commands once, in this order:

```bash
docker compose up -d --build
docker compose exec app composer install
docker compose exec app bun install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

---

## Start the Project

Start all services:

```bash
docker compose up -d
```

Stop everything:

```bash
docker compose down
```

---

## Access the Application

- **Laravel app:** [http://localhost:8080](http://localhost:8080)
- **Vite dev server (HMR):** [http://localhost:5173](http://localhost:5173)
- **Mailpit (emails UI):** [http://localhost:8025](http://localhost:8025)

---

## Running Laravel Commands

Use `docker compose exec app` instead of `php`:

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker
docker compose exec app php artisan test
```

### Composer

```bash
docker compose exec app composer require vendor/package
```

### Bun / Vite

```bash
docker compose exec app bun run dev
docker compose exec app bun run build
```

---

## Queues (Redis)

Queues use Redis by default.

To run a queue worker locally:

```bash
docker compose exec app php artisan queue:work
```

> **Note:** Leave this running in a separate terminal.

---

## Emails (Mailpit)

All outgoing emails are captured by Mailpit.

- **SMTP host:** `mailpit`
- **SMTP port:** `1025`
- **Web UI:** [http://localhost:8025](http://localhost:8025)

No real emails are sent.

---

## Database

MySQL runs in Docker. Data is persisted in a Docker volume.

### Connection Details

Already configured in `.env`:

```env
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

You do not need to install MySQL locally.

---

## Reset Everything (Clean Slate)

⚠️ **Warning:** This deletes the database.

```bash
docker compose down -v
docker compose up -d --build
```

---

## Common Issues

### App loads but assets don't update

Make sure the Vite container is running:

```bash
docker compose ps
```

You should see `laravel_vite`.

### Database connection error

Wait a few seconds and retry migrations:

```bash
docker compose exec app php artisan migrate
```
