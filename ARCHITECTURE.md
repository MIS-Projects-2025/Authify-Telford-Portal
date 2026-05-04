# Authify Architecture Documentation

## Overview

Authify is an internal SSO (Single Sign-On) portal application built with Laravel 12, React 18, and Inertia.js. It provides centralized authentication and access management for multiple internal systems organized by department.

## Technology Stack

| Layer | Technology |
|------|------------|
| Backend | Laravel 12, PHP 8.2 |
| Frontend | React 18, Inertia.js |
| Styling | Tailwind CSS 3.2, DaisyUI |
| UI Components | Radix UI, Ant Design, Lucide Icons |
| Database | MySQL (multiple connections) |
| Authentication | JWT via cookies |

## Project Structure

```
authify/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php        # SSO login/logout/validate
│   │   │   ├── AuthenticationController.php
│   │   │   ├── InternalController.php
│   │   │   ├── PortalController.php    # SystemCards API
│   │   │   ├── DashboardController.php
│   │   │   ├── General/
│   │   │   │   ├── AdminController.php
│   │   │   │   └── ProfileController.php
│   │   │   └── Admin/
│   │   │       ├── SystemController.php
│   │   │       ├── CardController.php
│   │   │       └── DepartmentController.php
│   │   ├── Middleware/
│   │   │   ├── AuthifyInternalMiddleware.php  # Token validation
│   │   │   └── AdminMiddleware.php          # Admin role check
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php         # Laravel default
│   │   ├── Department.php   # Portal departments
│   │   ├── Card.php        # Department cards
│   │   └── System.php      # Card systems
│   └── Services/
│       └── DataTableService.php  # Reusable datatable
├── resources/js/
│   ├── Pages/
│   │   ├── SystemCards.jsx    # Main portal
│   │   ├── Admin/           # Admin pages
│   │   ├── Profile.jsx
│   │   └── Dashboard.jsx
│   └── Components/
│       ├── ui/              # Radix-based UI
│       ├── admin/           # Admin components
│       └── sidebar/
├── routes/
│   ├── web.php            # Main routes
│   ├── auth.php           # Auth routes
│   ├── general.php       # Authenticated routes
│   └── api.php
└── database/
    └── migrations/
```

## Database Connections

The application uses multiple MySQL connections:

| Connection | Database | Purpose |
|------------|----------|---------|
| default | authify | Local app data (admins) |
| authify | authify | SSO session validation |
| masterlist | masterlist | Employee masterlist |

## Data Models

### Department
| Field | Type | Description |
|-------|-----|-------------|
| id | int | Primary key |
| name | varchar | Display name |
| basename | varchar | URL slug |
| color | varchar | Theme color |
| icon | varchar | Lucide icon name |
| sort_order | int | Display order |
| is_active | boolean | Visibility |

### Card
| Field | Type | Description |
|-------|-----|-------------|
| id | int | Primary key |
| department_id | int | FK to Department |
| card_icon | varchar | Icon |
| card_title | varchar | Title |
| description | text | Description |
| sort_order | int | Display order |
| is_active | boolean | Visibility |

### System
| Field | Type | Description |
|-------|-----|-------------|
| id | int | Primary key |
| card_id | int | FK to Card |
| list_name | varchar | Display name |
| system_url | varchar | Target URL |
| modal_icon | varchar | Icon |
| system_status | boolean | Active |
| require_auto_login | boolean | Auto-SSO |

### Admin
| Field | Type | Description |
|-------|-----|-------------|
| admin_id | int | Primary key |
| emp_id | int | Employee ID |
| emp_name | varchar | Name |
| emp_role | varchar | Role (admin/super_admin) |
| created_date | timestamp | Creation date |
| last_updated | timestamp | Last update |
| last_updated_by | varchar | Updated by |

## Authentication Flow

1. **Login Request** → `/login` → JWT token generated
2. **Token Storage** → Cookie (`authify_token`, `sso_token`) + Session
3. **Request Validation** → `AuthifyInternalMiddleware` checks:
   - Cookie token first
   - Session fallback
   - Database validation via `authify_sessions` table
4. **Session Rebuild** → Store emp_data in session:
   - emp_id, emp_name, emp_firstname
   - emp_jobtitle, emp_dept, emp_prodline
   - role (derived from job title)

## Role Assignment

Roles are derived from job title:
| Job Title Contains | Role |
|--------------------|------|
| "programmer" | admin |
| "mis senior supervisor" | admin |
| otherwise | user |

Admin access requires entry in `admin` table.

## Route Structure

### Public Routes (no auth)
```
GET  /login         - Login form
POST /login        - Process login
GET  /logout       - Logout
GET  /validate    - Validate token
GET  /admin/portal - Admin portal page
```

### Authenticated Routes (auth.internal)
```
/{dashboard_name}/
├── /                     - Dashboard
├── /admin                - Admin management
├── /new-admin           - Add new admin
├── /profile             - User profile
├── /portal              - SystemCards portal
```

### Admin Routes (+ AdminMiddleware)
```
/{dashboard_name}/
├── /admin           - Manage admins
├── /new-admin       - Add admins
├── /add-admin       - Add admin (POST)
├── /remove-admin    - Remove admin (POST)
└── /change-admin-role - Update role (PATCH)
```

## API Endpoints

| Endpoint | Controller | Description |
|----------|------------|-------------|
| GET /portal | PortalController | Main portal |
| GET /api/departments | PortalController | All departments |
| GET /api/cards/{basename} | PortalController | Dept cards |
| GET /api/systems/{cardId} | PortalController | Card systems |

## Caching

Portal data is cached with 1-hour TTL:
- `portal_sidebar_depts`
- `api_all_departments`
- `api_dept_cards_{basename}`
- `api_card_systems_{cardId}`

## Key Services

### DataTableService
Handles server-side datatable operations:
- Search (multiple columns)
- Sorting
- Pagination
- CSV export
- Multiple database connections

## Frontend Pages

| Page | Route | Description |
|------|------|-------------|
| Login | /login | SSO login form |
| Dashboard | /{app} | Main dashboard |
| SystemCards | /{app}/portal | System portal |
| Admin | /{app}/admin | Admin management |
| NewAdmin | /{app}/new-admin | Add admin |
| Profile | /{app}/profile | User profile |
| Unauthorized | /unauthorized | Access denied |

## Environment Variables

```env
APP_NAME=authify        # URL prefix
DB_CONNECTION=mysql    # Default connection
DB_HOST=...
DB_DATABASE=authify
DB_USERNAME=...
DB_PASSWORD=...

# Additional connections in config/database.php
```

## Development Commands

```bash
# Install dependencies
composer install
npm install

# Start development
composer dev  # Runs: Laravel + queue + logs + Vite

# Run tests
composer test

# Build assets
npm run build
```

## Dependencies

### PHP (composer.json)
- laravel/framework ^12.0
- laravel/sanctum ^4.0
- inertiajs/inertia-laravel ^2.0
- firebase/php-jwt ^7.0

### Node (package.json)
- react ^18.2.0
- @inertiajs/react ^2.0.13
- @radix-ui/react-* (dialog, dropdown, etc.)
- antd ^6.0.0
- lucide-react ^0.555.0
- tailwindcss ^3.2.1
- zustand ^5.0.6