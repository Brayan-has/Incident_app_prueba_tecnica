# Incident Management App

Incident management system with backend built in **Laravel 12** and frontend built in **Vue 3 + Quasar**, orchestrated with **Docker Compose**.

---

## Requirements

- [Docker](https://www.docker.com/) and Docker Compose installed
- Ports **8000**, **5173**, **3306**, and **6379** available

---

## How to Run

```bash
# 1. Clone the repository
git clone https://github.com/Brayan-has/Incident_app_prueba_tecnica.git
cd "Prueba técnica"

# 2. Start all services
cd prueba
docker compose up -d --build
```

> The application container waits 10 seconds before automatically running migrations and seeders.

| Service    | URL                         |
|------------|-----------------------------|
| Backend    | http://localhost:8000       |
| Frontend   | http://localhost:5173       |
| MySQL      | localhost:3306              |
| Redis      | localhost:6379              |

---

## `.env` File Configuration

Before starting the project, create a `.env` file inside the `prueba/` folder with the following configuration:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:JJV6e7Jemk9xqQkRvEVG+eKfwMkDDPDNeri9jtH+eYw=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

# PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=incident_db
DB_USERNAME=root
DB_PASSWORD=admin

SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis

CACHE_STORE=redis
# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=predis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

---

To stop the services:

```bash
docker compose down
```

---

## Default Credentials

| Role  | Email              | Password |
|-------|--------------------|----------|
| Admin | admin@example.com  | password |
| User  | user@example.com   | password |

---

## Features

### 🔐 Authentication

| Method | Endpoint                  | Description        |
|--------|----------------------------|--------------------|
| POST   | `/api/v1/auth/login`       | Login              |
| POST   | `/api/v1/auth/logout`      | Logout             |

The login endpoint returns a **Sanctum** token that must be sent in the `Authorization: Bearer <token>` header for all protected routes.

---

### 📊 Dashboard

| Method | Endpoint                                | Description                            |
|--------|------------------------------------------|----------------------------------------|
| GET    | `/api/v1/incidents/dashboard`            | Metrics: totals, by status, etc.       |
| GET    | `/api/v1/incidents/expired`              | Expired incidents                      |
| GET    | `/api/v1/incidents/status/{status}`      | Filter incidents by status             |

---

### 🚨 Incidents (CRUD)

| Method | Endpoint                                      | Description                   |
|--------|------------------------------------------------|-------------------------------|
| GET    | `/api/v1/incidents`                            | List incidents                |
| POST   | `/api/v1/incidents`                            | Create incident               |
| GET    | `/api/v1/incidents/{id}`                       | View details                  |
| PUT    | `/api/v1/incidents/{id}`                       | Update incident               |
| DELETE | `/api/v1/incidents/{id}`                       | Delete (soft delete)          |
| POST   | `/api/v1/incidents/{id}/restore`               | Restore archived incident     |
| DELETE | `/api/v1/incidents/{id}/force-delete`          | Permanently delete incident   |

---

### 👥 Users (CRUD)

| Method | Endpoint                                   | Description                  |
|--------|---------------------------------------------|------------------------------|
| GET    | `/api/v1/users`                             | List users                   |
| POST   | `/api/v1/users`                             | Create user                  |
| GET    | `/api/v1/users/{id}`                        | View details                 |
| PUT    | `/api/v1/users/{id}`                        | Update user                  |
| DELETE | `/api/v1/users/{id}`                        | Delete (soft delete)         |
| POST   | `/api/v1/users/{id}/restore`                | Restore archived user        |
| DELETE | `/api/v1/users/{id}/force-delete`           | Permanently delete user      |

---

### 🎭 Roles and Permissions

| Method | Endpoint                                        | Description                     |
|--------|--------------------------------------------------|---------------------------------|
| GET    | `/api/v1/roles`                                  | List all roles                  |
| GET    | `/api/v1/roles/me`                               | Authenticated user's role       |
| POST   | `/api/v1/roles/assign/{user_id}`                 | Assign role to a user           |
| GET    | `/api/v1/permissions`                            | List permissions                |
| POST   | `/api/v1/permissions/assign/{user_id}`           | Assign permission               |

---

## Project Structure

```text
Prueba técnica/
├── prueba/          # Laravel 12 Backend
│   ├── app/
│   │   └── Http/Controllers/
│   │       ├── AuthController.php
│   │       ├── IncidentController.php
│   │       ├── UserController.php
│   │       └── RoleController.php
│   ├── routes/api.php
│   ├── docker-compose.yml
│   └── Dockerfile
└── frontend/        # Vue 3 + Quasar Frontend
    └── src/
        ├── pages/
        ├── stores/
        ├── api/
        └── router/
```

---

## Useful Commands

```bash
# View backend logs
docker logs incidents_app -f

# View frontend logs
docker logs incidents_frontend -f

# Run migrations manually
docker exec incidents_app php artisan migrate --seed --force

# Clear Laravel cache
docker exec incidents_app php artisan cache:clear
docker exec incidents_app php artisan config:clear
```

## Login

<img width="706" height="593" alt="Login" src="https://github.com/user-attachments/assets/90e49be5-1051-4d83-9d47-3ef1f7f5709a" />

## Dashboard

<img width="1898" height="878" alt="Dashboard" src="https://github.com/user-attachments/assets/3adf25df-4e80-4486-8651-a20eb1991876" />

## Incidents

<img width="1901" height="873" alt="Incidents" src="https://github.com/user-attachments/assets/8a9ccf58-2d02-4e95-91ab-04f3beebda9f" />

## Create Incidents

<img width="1892" height="873" alt="Create incident" src="https://github.com/user-attachments/assets/7b8b87a2-22ea-4c6d-b692-56213ca1d7fc" />

## Create Users

<img width="1561" height="862" alt="Create user" src="https://github.com/user-attachments/assets/0dc19874-46eb-44dd-9002-06f2c240771c" />

## View Incidents

<img width="1413" height="751" alt="image" src="https://github.com/user-attachments/assets/382b8ba4-0321-463c-8676-40882acd66cb" />
