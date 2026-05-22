# PresyoPH

A crowdsourced price tracking and budgeting application designed to help consumers monitor local commodity prices and optimize household spending.

## 🛠️ Tech Stack

- **Backend:** PHP 8.3 + Laravel 13 (REST API)
- **Database & Caching:** MySQL, Redis (Queues & Caching)
- **Frontend:** ReactJS (Client App) | VueJS (Admin/Back Office)
- **Environment:** Docker (Laravel Sail)

## 🚀 Current Implementation Status

The project is currently in active backend development, focusing on building a robust, resilient REST API.

## 📦 Quick Local Setup (Docker)

This project uses **Laravel Sail** for a standardized development environment.

1. Clone the repository and install dependencies
    ```shell
    composer install
    ```
2. Copy the environment file
    ```shell
    cp .env.example .env
    ```
3. Start the Docker containers. [Configuring alias.](https://laravel.com/docs/13.x/sail#configuring-a-shell-alias '‌')
    ```shell
    ./vendor/bin/sail up -d
    ```
4. Generate APP_KEY and paste it on `.env` file
    ```shell
    sail artisan key:generate
    ```
5. Install dev dependency (prettier)
    ```shell
    sail npm ci
    ```
6. Run migrations and seed data
    ```shell
    sail artisan migrate --seed
    ```

## 🏗 Architectural & Upskilling Roadmap

**PresyoPH** is also an engineering playground for me to expand my technical horizons. Here is the blueprint for the upcoming modules:

### 🟩 Phase 1: Heavy-Duty Backend (Next Up)

**⚪Asynchronous Audit Logging**

Integrate **Redis** to handle background queues for internal audit log system.

### 🟦 Phase 2: Client Web App (ReactJS)

**⚪State Management**

Migrating my past experiences with (**Zustand + Immer**) and native hooks (**React Context API + useReducer**) into **Redux** to master enterprise-scale state patterns.

**⚪Type Safety**

Introduce **TypeScript** to enforce strict type-safe contracts between the Laravel API and front-end components.

### 🟨 Phase 3: Back Office App (VueJS)

**⚪Upskilling**

Building the entire back-office dashboard from scratch using **VueJS** to gain hands-on proficiency in a new modern frontend ecosystem.

### 🧪 Test-Driven Development (TDD)

Initial modules were built using the standard development workflow, but I will be taking a **Test-First approach** for the new API endpoints.

To run the test suite locally:

```shell
sail artisan test
```
