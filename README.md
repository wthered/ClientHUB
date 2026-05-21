# ClientHUB CRM

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

**ClientHUB CRM** is a full‑featured Customer Relationship Management system for small to medium businesses.  
It helps manage customers, sales, inventory, invoices, and team activities in one place.

## ✨ Features

- 🏢 **Account & Contact Management**
- 💰 **Opportunities & Sales Pipeline**
- 🧾 **Invoicing & Payment Tracking**
- 📦 **Inventory & Stock Management**
- 📞 **Activity Logging** (Calls, Meetings, Tasks)
- 📊 **Dashboard & Reporting**
- 👥 **User Roles & Permissions** (RBAC)
- 🔐 **Audit Logs** for compliance
- 📧 **Email Notifications** (Weekly reports)

## 🛠️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade, Vue.js, jQuery, Bootstrap 5
- **Database**: PostgreSQL / MySQL
- **Queue**: Redis (for emails, jobs)

## 📦 Installation

```bash
git clone https://github.com/wthered/clienthub-crm.git
cd clienthub-crm
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
