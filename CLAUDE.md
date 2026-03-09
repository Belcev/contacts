# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel 12 / PHP 8.4 application for managing contacts with bulk XML import capability. Runs in Docker (Nginx + PHP-FPM + MySQL 8 + Redis + queue worker).

## Setup and Running

```bash
# Docker (primary setup)
sudo chown 33:33 . -R
docker-compose up --build -d
# App available at http://localhost:80, phpMyAdmin at http://localhost:8081
```

```bash
# Local development (from contacts/ directory)
composer run setup       # Install deps, generate key, migrate, build assets
composer run dev         # Start server + queue worker + logs + Vite concurrently
```

## Common Commands

All run from the `contacts/` project root:

```bash
composer run test        # Clear config cache, then run PHPUnit
php artisan test --filter TestClassName   # Run a single test class
php artisan test tests/Feature/ContactControllerTest.php  # Run specific file

composer run phpstan     # Static analysis (max level, 4G memory limit)
composer run ecs         # Check code style (PSR-12)
composer run rector      # Dry-run automated refactoring
composer run fix         # Apply rector + ecs fixes
```

```bash
npm run dev    # Vite dev server
npm run build  # Production asset build
```

## Architecture

### Request Flow

Web request → Nginx → PHP-FPM (`app` container) → Laravel routes → Controller
File upload → `ImportController::store()` → dispatches `ImportContactsJob` to DB queue → `worker` container picks it up → calls `ImportContacts` Artisan command

### Key Components

**`app/Models/Contact.php`** — Eloquent model. Has a `search()` scope for full-text search (MySQL FULLTEXT index on `first_name`, `last_name`, `email`). Email is the unique business key.

**`app/Http/Controllers/ContactController.php`** — Standard CRUD with pagination (25/page). Has a `purge()` action (DELETE all) beyond normal resourceful routes.

**`app/Http/Controllers/ImportController.php`** — Validates uploaded XML (max 512MB), dispatches `ImportContactsJob`, redirects back.

**`app/Jobs/ImportContactsJob.php`** — Queue job (3 retries, 120s timeout). Calls `ImportContacts` Artisan command internally, then deletes the uploaded file.

**`app/Console/Commands/ImportContacts.php`** — Core import logic. Uses `XMLReader` for streaming (handles 100k+ records without memory issues). Validates emails, trims whitespace, batches upserts (default batch size: 2000) keyed by email. Supports `--delete` flag to remove file after import.

**`app/Http/Requests/`** — `StoreContactRequest` and `UpdateContactRequest` handle validation (email uniqueness, required fields, max 255).

### Database

Single `contacts` table: `id`, `email` (unique), `first_name`, `last_name`, `timestamps`.
Also: `cache` and `jobs` tables for Laravel internals.
Queue driver: database. Cache driver: database (configurable to Redis).

### Frontend

Blade templates with Tailwind CSS + Alpine.js. No SPA framework — server-rendered with minimal JS for interactivity.

## Code Quality Standards

- Strict PHP types (`declare(strict_types=1)`) throughout
- PHPStan at max level (Larastan for Laravel-aware analysis)
- PSR-12 enforced via Easy Coding Standard (`ecs.php`)
- Rector for automated refactoring (`rector.php`)

## Testing

Tests use `RefreshDatabase` trait. Queue driver is `sync` and cache driver is `array` in test environment. Feature tests cover all CRUD routes and the import command.
