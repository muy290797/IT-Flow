# 🚀 IT-Flow

> **IT asset management system** built with Laravel 13, Livewire 4, and Flux UI. Feature coverage is documented as implemented in this repository; compatibility with Snipe-IT has not been independently verified.

[![Laravel](https://img.shields.io/badge/Laravel-13-red.svg)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-4-pink.svg)](https://livewire.laravel.com)
[![Flux UI](https://img.shields.io/badge/Flux-UI-blue.svg)](https://fluxui.dev)

---

## 📋 Table of Contents

- [About](#about)
- [Features](#features)
- [Screenshots](#screenshots)
- [Tech Stack](#tech-stack)
- [Quick Start](#quick-start)
- [Documentation](#documentation)
- [API](#api)
- [Comparison with Snipe-IT](#comparison-with-snipe-it)
- [Contributing](#contributing)
- [License](#license)

---

## 🎯 About

**IT-Flow** is a comprehensive IT Asset Management System that helps you track and manage:

- 💻 **Hardware Assets** - Laptops, desktops, servers, phones
- 🔧 **Accessories** - Keyboards, mice, monitors, cables  
- 📦 **Consumables** - Toner, paper, batteries
- ⚙️ **Components** - RAM, HDDs, CPUs
- 🔑 **Software Licenses** - Track seats and expiration
- 🛠️ **Maintenance** - Schedule and track repairs
- 📊 **Reports** - Comprehensive analytics
- 👥 **Users** - Assign assets to employees

### Why IT-Flow?

- ✅ **Modern Tech Stack** - Laravel, Livewire, Flux UI
- ✅ **Better UX** - Real-time updates, no page refreshes
- ✅ **Dark Mode** - Built-in theme switcher
- ✅ **PWA Ready** - Install as mobile app
- ✅ **Permission Groups** - Granular permission management
- ✅ **2FA** - Two-factor authentication
- ✅ **Asset API** - Sanctum-authenticated asset endpoints

---

## ✨ Features

### Core Features

#### 📦 Asset Management
- Complete CRUD operations
- Polymorphic checkout (to User, Location, or Asset)
- QR code generation
- Status labels (deployable, undeployable, pending, archived)
- Expected checkin date tracking
- Checkout notes
- Complete activity history
- Purchase and warranty tracking
- Depreciation calculation

#### 🗂️ Inventory Management
- **Accessories** - Quantity tracking, checkout to User/Asset
- **Consumables** - Usage tracking (no checkin)
- **Components** - Checkout to Asset only
- **Licenses** - Seat-based licensing with expiration
- Low stock alerts for all inventory types

#### 🔧 Maintenance System
- Schedule maintenance (repair, upgrade, preventive, inspection)
- Status tracking (scheduled, in progress, completed, cancelled)
- Cost and downtime tracking
- Supplier and technician assignment
- Upcoming and overdue alerts

#### 📝 Asset Requests
- Request workflow with approval
- Priority levels (low, medium, high, urgent)
- Request types (checkout, repair, upgrade, replacement)
- Status tracking (pending, approved, rejected, completed)

#### 📊 Reports
8 comprehensive report types:
1. Asset Reports
2. Inventory Reports
3. License Reports
4. Maintenance Reports
5. User Reports
6. Activity Reports
7. Depreciation Reports
8. Custom Reports

Export to CSV or PDF

#### 📜 Activity Log
- Full audit trail
- All CRUD operations logged
- Checkout/checkin tracking
- User action history
- Search and filter capabilities

#### 📱 Dashboard
- Asset statistics (total, available, assigned, value)
- Inventory statistics
- Low stock alerts
- License expiration alerts
- Maintenance alerts
- Overdue checkout alerts
- Recent activity feed
- Top categories and locations

### Additional Features

#### 🎨 Modern UI/UX
- **Dark Mode** - Toggle between light/dark themes
- **Real-time Updates** - Livewire for SPA-like experience
- **Mobile Optimized** - Bottom navigation for mobile
- **Custom Colors** - Customize sidebar, navbar, bottom nav
- **Responsive Design** - Works on all devices

#### 👥 Organization and Permissions
- Company, department, and location records
- Role-based access control and permission groups

#### 🔐 Security
- Two-factor authentication
- Laravel Sanctum API authentication
- Password reset
- Email verification
- Role-based access control (ready)

#### 🌐 API
- RESTful API
- Token-based authentication
- All CRUD endpoints
- Checkout/checkin endpoints
- Complete documentation

#### 🔔 Notifications
4 email notification types:
1. Asset Checked Out
2. Asset Due for Return
3. License Expiring (30-day warning)
4. Maintenance Due

#### 📱 Progressive Web App (PWA)
- Installable app manifest and service worker
- Offline fallback page

---

## 📸 Screenshots

*Coming soon - Add your screenshots here*

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 13
- **PHP**: 8.3+
- **Database**: SQLite / MySQL / PostgreSQL
- **Authentication**: Laravel Sanctum
- **Queue**: Database by default (Redis optional)

### Frontend
- **Framework**: Livewire 4
- **UI Components**: Flux UI
- **JavaScript**: Alpine.js
- **CSS**: Tailwind CSS 4
- **Build Tool**: Vite

### Features
- **Permissions**: Application roles and permission groups
- **2FA**: Laravel Fortify
- **PWA**: Web manifest and service worker assets
- **Real-time**: Livewire Wire
- **Dark Mode**: Built-in

---

## ⚡ Quick Start

### Requirements
- PHP 8.3+
- Composer
- Node.js 18+ & NPM
- Database (SQLite, MySQL, or PostgreSQL)

### Installation

```bash
# 1. Clone repository
git clone https://github.com/yourusername/it-flow.git
cd it-flow

# 2. Install dependencies
composer install
npm install

# 3. Create the environment file (the setup page fills in the database and app key)
cp .env.example .env

# 4. Build assets
npm run build

# 5. Start development server
php artisan serve
```

Open `http://localhost:8000`. On a new installation, the setup page asks for the database connection and first administrator account, then runs migrations. For MySQL, create the database and grant its user permission to create and modify tables before opening setup. Ensure `.env` and `storage/app` are writable by the web process.

Setup is disabled automatically after the first account is created. No demo users are created by the setup page.

### Queue worker

The default `database` queue stores queued mail and notifications until a worker processes them. Keep a worker running in a separate terminal during development, and configure a persistent worker (for example, Supervisor) in production:

```bash
php artisan queue:work --tries=3
```

---

## 📚 Documentation

### Essential Reading

1. [Browser-based PDF setup](docs/BROWSERSHOT_SETUP.md)
2. [Email notifications](docs/EMAIL_NOTIFICATIONS.md)
3. [Document service testing notes](docs/TESTING_DOCUMENT_SERVICES.md)

### Technical Documentation

- API routes: [routes/api.php](routes/api.php)
- Schema: [database/migrations](database/migrations)
- Application code: [app](app)

### Operations

For scheduled signature reminders, configure Laravel's scheduler in the deployment environment. The application schedule runs `documents:send-signature-reminders` daily at 09:00 Asia/Jakarta.

---

## 🔌 API

IT-Flow provides Sanctum-authenticated endpoints for login, current-user access, and asset CRUD/checkout/checkin operations.

The login endpoint is limited to five requests per minute per client IP. Asset list pagination defaults to 15 records; `per_page` accepts integers from 1 to 100.

### Authentication

```bash
# Login and get token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

### Example Requests

```bash
# Get all assets
curl -X GET http://localhost:8000/api/assets \
  -H "Authorization: Bearer YOUR_TOKEN"

# Create asset
curl -X POST http://localhost:8000/api/assets \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "asset_tag": "LAP001",
    "name": "Dell Latitude 5520",
    "asset_model_id": 1,
    "status_label_id": 1
  }'

# Checkout asset
curl -X POST http://localhost:8000/api/assets/1/checkout \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "assigned_type": "App\\Models\\User",
    "assigned_id": 1,
    "expected_checkin_date": "2026-10-01"
  }'
```

The route definitions are in [routes/api.php](routes/api.php). The API currently covers assets; it does not expose every web application module.

---

## 📊 Comparison with Snipe-IT

| Feature | Snipe-IT | IT-Flow | Advantage |
|---------|----------|---------|-----------|
This project is inspired by IT asset management workflows. No verified feature-parity assessment against Snipe-IT is maintained here. Compare the feature list above with your requirements before choosing a platform.

---

## 🗄️ Database

The schema is defined by the migration files in `database/migrations`. It covers users and permissions, assets, organization records, inventory, maintenance, requests, stock changes, and document handovers. Review those migrations for the authoritative schema; table counts in older project notes are stale.

---

## 🧪 Testing

```bash
php artisan test
```

Use an isolated test database for automated tests. Do not run `migrate:fresh` against a database containing application data.

---

## 🚀 Deployment

### Production Checklist

For the first deployment, install dependencies and build assets, create the database, make `.env` and `storage/app` writable by the web process, then open the application URL to complete setup. The setup page generates `APP_KEY`, runs migrations, and creates the first administrator.

For later deployments, keep the existing `APP_KEY` and run the normal migration and optimization steps below.

```bash
# Run migrations on an existing installation
php artisan migrate --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build assets for production
npm run build
```

**Full deployment guide**: [DEPLOYMENT-GUIDE.md](DEPLOYMENT-GUIDE.md)

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Setup

```bash
# Install dependencies
composer install
npm install

# Create environment file
cp .env.example .env

# Watch assets
npm run dev

# Start server
php artisan serve
```

Open the local application URL and complete the first-run setup page to configure the database, run migrations, and create the administrator.

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

## 🙏 Acknowledgments

- Inspired by [Snipe-IT](https://snipeitapp.com)
- Built with [Laravel](https://laravel.com)
- Powered by [Livewire](https://livewire.laravel.com)
- Styled with [Flux UI](https://fluxui.dev)
- Icons from [Heroicons](https://heroicons.com)

---

## 📧 Contact

For questions, issues, or suggestions, please open an issue on GitHub.

---

## 📈 Project Stats

- **Version**: 1.0.0
- **Status**: Active development; deployment readiness depends on environment configuration and operational verification
- **Automated tests**: Feature and unit tests are present under `tests/`

---

## 🎯 Roadmap

### ✅ Completed (v1.0)
- [x] Asset Management
- [x] Inventory Management (Accessories, Consumables, Components, Licenses)
- [x] Maintenance System
- [x] Asset Requests
- [x] Activity Log
- [x] Reports (8 types)
- [x] Dashboard
- [x] API (REST + Sanctum)
- [x] Notifications (4 types)
- [x] Dark Mode
- [x] PWA Support
- [x] Permission groups
- [x] Two-Factor Auth

### 🔜 Upcoming (v1.1)
- [ ] Custom Fields UI
- [ ] File Attachments UI
- [ ] Import/Export UI
- [ ] Expand automated test coverage
- [ ] Advanced Role Permissions
- [ ] Audit Reports
- [ ] Asset Kits
- [ ] Bulk Operations UI
- [ ] Mobile App (React Native)
- [ ] Advanced Analytics

### 💡 Future Ideas
- [ ] Barcode scanning (mobile)
- [ ] LDAP/AD integration
- [ ] SSO support
- [ ] Advanced workflows
- [ ] IoT asset tracking
- [ ] Predictive maintenance
- [ ] AI-powered insights

---

## ⭐ Show Your Support

If you find IT-Flow useful, please give it a ⭐ on GitHub!

---

## 🎉 Success Stories

*Share your IT-Flow success story!*

---

**Built with ❤️ using Laravel 13, Livewire 4, and Flux UI**

---

*Last Updated: September 25, 2026*

