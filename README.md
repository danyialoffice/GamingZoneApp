# 🎮 Gaming Zone - Multi-Tenant SaaS Platform

A complete multi-tenant SaaS platform for managing gaming zones with PC booking, user management, and subscription billing built with Laravel 10+.

## 🌟 Features

### Multi-Tenant Architecture
- **Shared Database** with tenant_id isolation
- **Automatic tenant resolution** from subdomain or session
- **Global scopes** for automatic data filtering
- **White labeling** support (custom logos, colors per tenant)

### User Roles
- **Super Admin**: Platform owner, manages all tenants
- **Tenant Admin**: Gaming zone owner, manages their own business
- **Booking Manager**: Staff for handling reservations
- **Player**: Customers who book PCs

### Core Modules
- ✅ **Tenants (Gaming Zones)**: Full CRUD with subscription management
- ✅ **Rooms**: Multiple rooms per tenant with hourly rates
- ✅ **PCs**: Gaming stations with specs, status, and availability
- ✅ **Bookings**: Complete workflow with approval system
- ✅ **Packages**: Pre-paid gaming packages
- ✅ **Payments**: Payment tracking and history
- ✅ **Notifications**: Per-tenant notification system

### Booking Workflow
1. Player selects gaming zone
2. Chooses room, PC, date, and time
3. Creates booking (status: pending)
4. 1-hour timer starts
5. Tenant admin approves/rejects
6. Confirmed bookings can check-in/check-out

## 🚀 Quick Start

### Requirements
- PHP 8.1+
- MySQL 8.0+
- Composer
- Node.js 18+

### Installation

1. **Clone and Install Dependencies**
```bash
cd WebisteForGamingZone
composer install
npm install
```

2. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure Database** (.env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gaming_zone
DB_USERNAME=root
DB_PASSWORD=
```

4. **Run Migrations & Seeders**
```bash
php artisan migrate
php artisan db:seed
```

5. **Create Storage Link**
```bash
php artisan storage:link
```

6. **Start Server**
```bash
php artisan serve
```

Visit `http://localhost:8000`

## 🔐 Default Login Credentials

### Super Admin
- **Email**: superadmin@gamingzone.com
- **Password**: password

### Tenant Admins (for each gaming zone)
- **Email**: admin@{tenant-slug}.com
- **Password**: password

### Booking Managers
- **Email**: manager@{tenant-slug}.com
- **Password**: password

### Players
- **Email**: player1@{tenant-slug}.com
- **Password**: password

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── SuperAdmin/        # Super admin controllers
│   │   ├── Tenant/            # Tenant-specific controllers
│   │   └── AuthController.php # Authentication
│   └── Middleware/
│       ├── TenantMiddleware.php    # Multi-tenant resolution
│       ├── CheckRole.php          # Role-based access
│       └── CheckPermission.php    # Permission-based access
├── Models/
│   ├── Traits/
│   │   └── BelongsToTenant.php   # Multi-tenant trait
│   ├── Tenant.php
│   ├── User.php
│   ├── Room.php
│   ├── PC.php
│   ├── Booking.php
│   └── ...
└── Services/

database/
├── migrations/
│   ├── create_tenants_table.php
│   ├── create_roles_table.php
│   ├── create_rooms_table.php
│   └── ...
└── seeders/
    └── TenantSeeder.php

resources/views/
├── layouts/
│   └── app.blade.php         # Gaming-themed layout
├── website/
│   ├── home.blade.php         # Public landing page
│   └── booking/
│       └── create.blade.php   # Booking form
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
└── tenant/
    ├── dashboard.blade.php
    ├── rooms/
    ├── pcs/
    └── bookings/
```

## 🎨 Customization

### Branding Per Tenant
Each tenant can have:
- Custom logo
- Custom theme color
- Custom description

### Subscription Plans
Three tiers available:
- **Basic**: 2 rooms, 10 PCs, 3 staff
- **Pro**: 10 rooms, 50 PCs, 15 staff
- **Enterprise**: Unlimited everything

## 🔌 API Endpoints

### Public
- `GET /` - Landing page
- `POST /auth/login` - Login
- `POST /auth/register` - Register

### Authenticated (Player)
- `GET /booking/create` - Book a PC
- `GET /booking/my-bookings` - My bookings
- `POST /booking` - Create booking
- `POST /booking/{id}/cancel` - Cancel booking

### Tenant Admin
- `GET /tenant/dashboard` - Dashboard
- `GET /tenant/rooms` - Manage rooms
- `GET /tenant/pcs` - Manage PCs
- `GET /tenant/bookings` - View bookings
- `POST /tenant/bookings/{id}/approve` - Approve booking
- `POST /tenant/bookings/{id}/reject` - Reject booking

### Super Admin
- `GET /super-admin/dashboard` - Platform overview
- `GET /super-admin/tenants` - Manage tenants
- `POST /super-admin/tenants` - Create tenant
- `PUT /super-admin/tenants/{id}` - Update tenant

## 🛡️ Security Features

- **Tenant Isolation**: All queries automatically filtered by tenant_id
- **Role-Based Access Control**: Per-tenant role assignments
- **Permission System**: Granular permissions per role
- **CSRF Protection**: Built into Laravel forms
- **SQL Injection Prevention**: Eloquent ORM
- **XSS Prevention**: Blade templating auto-escaping

## 📊 Database Schema

Key relationships:
- Tenants have many Users (through tenant_users)
- Tenants have many Rooms, PCs, Bookings
- Users have different roles per tenant
- All tenant-specific tables have tenant_id

## 🧪 Testing

```bash
php artisan test
```

## 📝 License

This project is open-sourced software licensed under the MIT license.

## 🤝 Support

For support, email support@gamingzone.com or join our Discord.

---

Built with ❤️ for the gaming community
