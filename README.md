# Messagerie

A complete production-ready Laravel 11 unified messaging platform that combines internal realtime messaging and external email synchronization (SMTP/IMAP/POP3) into a single, seamless dashboard.

## Features
- **Internal Messaging**: Realtime chat via Laravel Reverb.
- **External Email**: Send via SMTP, Sync via IMAP/POP3.
- **Company Directory**: Unified contacts list for all company users and external partners.
- **Multilingual**: English and French support built-in.
- **Admin Settings**: Manage platform branding, global settings, and default mail configuration.
- **Rich Aesthetics**: Premium custom CSS dashboard design (Vanilla CSS without Tailwind overhead).

## Requirements
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & NPM
- Redis (for Queue & WebSockets)

## Installation

1. **Clone the repository**:
   ```bash
   git clone <repository_url> .
   ```

2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

3. **Install Node Dependencies**:
   ```bash
   npm install
   npm run build
   ```

4. **Environment Setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Configure your database, Redis, and Reverb settings in the `.env` file.

5. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate --seed
   ```

6. **Start the Application Services**:
   - Web Server: `php artisan serve` or use Laravel Valet/Herd.
   - Queue Worker (for mail sync): `php artisan queue:work`
   - Reverb Server: `php artisan reverb:start`

## Architecture Overview
See `technical_architecture.md` for in-depth architectural decisions and module breakdowns.

## Specifications
See `project_spec.md` for the original project requirements and feature checklist.
