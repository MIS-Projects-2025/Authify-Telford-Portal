# Authify Turnover Documentation

## Project Overview

| Item | Description |
|------|-------------|
| Project Name | Authify |
| Type | Internal SSO Portal |
| Stack | Laravel 12 + React 18 + Inertia.js |
| Purpose | Centralized authentication and system access portal for internal applications |

## Onboarding Checklist

### 1. Setup Development Environment
```bash
# Clone repository
git clone <repo-url> authify

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env .env (configure databases)

# Generate application key
php artisan key:generate

# Start development server
composer dev
```

### 2. Configure Databases
The app requires three MySQL connections. Add to `config/database.php` or `.env`:

**Default connection** (`mysql`): Authify local app
- Tables: sessions, users, password_reset_tokens, admin

**Authify connection** (`authify`): SSO sessions
- Table: authify_sessions (session storage)

**Masterlist connection** (`masterlist`): Employee data
- Table: employee_masterlist (employee records)

### 3. Run Migrations
```bash
php artisan migrate
```

Execute `System_Tables.sql` for additional tables:
- admin - Admin users

## Key Features

### Authentication System
- JWT-based SSO via cookies
- Token stored in: `authify_token`, `sso_token`
- Session fallback support
- Role derived from job title

### Portal Management
- Departments → Cards → Systems hierarchy
- CRUD operations for each level
- Active/inactive status toggle
- Sort order configuration

### Admin Management
- Add/remove administrators
- Role management (admin/super_admin)
- CSV export capability
- Employee masterlist integration

### User Features
- Profile viewing
- Password change
- Dashboard access

## Common Tasks

### Adding a New Department
1. Insert into `departments` table:
```sql
INSERT INTO departments (name, basename, color, icon, sort_order, is_active)
VALUES ('Department Name', 'dept-basename', '#color', 'icon-name', 1, 1);
```

### Adding a New Card
1. Insert into `cards` table:
```sql
INSERT INTO cards (department_id, card_icon, card_title, description, sort_order, is_active)
VALUES (1, 'icon-name', 'Card Title', 'Description', 1, 1);
```

### Adding a New System
1. Insert into `systems` table:
```sql
INSERT INTO systems (card_id, list_name, system_url, modal_icon, system_status, require_auto_login, sort_order)
VALUES (1, 'System Name', 'https://target-url', 'icon-name', 1, 0, 1);
```

### Adding an Admin
1. Navigate to: `/{app_name}/new-admin`
2. Search for employee
3. Select role
4. Click Add

### Clearing Cache
```bash
php artisan cache:clear
```

## Important Files

| File | Purpose |
|------|---------|
| app/Http/Controllers/AuthController.php | Login/logout logic |
| app/Http/Middleware/AuthifyInternalMiddleware.php | Token validation |
| app/Http/Middleware/AdminMiddleware.php | Admin access check |
| app/Http/Controllers/PortalController.php | SystemCards API |
| app/Services/DataTableService.php | Datatable operations |
| routes/web.php | Route definitions |
| routes/general.php | Authenticated routes |
| config/database.php | Database connections |

## Role Configuration

| Job Title Contains | Role |
|--------------------|------|
| "programmer" | admin |
| "mis senior supervisor" | admin |
| otherwise | user |

Add more patterns in `AuthifyInternalMiddleware.php:48-51`.

## API Routes

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/departments | All departments |
| GET | /api/cards/{basename} | Department cards |
| GET | /api/systems/{cardId} | Card systems |

## Frontend Routes

| Route | Page | Auth Required |
|-------|------|--------------|
| /login | Login | No |
| /{app}/ | Dashboard | Yes |
| /{app}/portal | SystemCards | Yes |
| /{app}/admin | Admin List | Yes + Admin |
| /{app}/new-admin | Add Admin | Yes + Admin |
| /{app}/profile | Profile | Yes |
| /unauthorized | Unauthorized | - |

## Testing

```bash
# Run all tests
composer test

# Run specific test file
php artisan test --filter=AuthifyLoginTest
```

## Build Commands

```bash
# Development
npm run dev

# Production build
npm run build

# Clear cache
php artisan cache:clear

# Clear config
php artisan config:clear
```

## Troubleshooting

### "Session not found" errors
- Check `authify_sessions` table in `authify` database
- Verify token validity
- Clear cookies and re-login

### Database connection errors
- Check `.env` configuration
- Verify database user permissions
- Test connection: `php artisan tinker`

### Cache issues
- Clear cache: `php artisan cache:clear`
- Disable cache in development: comment out Cache::remember calls

### Admin access denied
- Verify employee in `admin` table
- Check job title contains "programmer" or "mis senior supervisor"
- Add manually to admin table if needed

## Deployment Checklist

1. [ ] Configure production `.env`
2. [ ] Run migrations: `php artisan migrate`
3. [ ] Set APP_DEBUG=false
4. [ ] Configure queue (optional)
5. [ ] Build assets: `npm run build`
6. [ ] Clear caches
7. [ ] Set up web server (Apache/Nginx)

## Security Notes

- Tokens stored in HTTP-only cookies
- Session duration: 7 days
- Admin routes protected by middleware
- CSRF protection enabled
- No credentials in logs

## Additional Resources

- Architecture: `./ARCHITECTURE.md`
- README: `./README.md`
- SQL schemas: `./System_Tables.sql`

## Contact

For issues or questions, refer to the codebase or contact the development team.