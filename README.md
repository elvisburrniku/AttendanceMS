# Attendance Management System Integration Toolkit

Complete toolkit for integrating attendance management system into multi-tenant Laravel applications.

## Quick Start

### 1. Generate Configuration
```bash
php generate-config.php /path/to/your/projects
```

### 2. Run Integration
```bash
# Linux/Mac
./run-integration.sh

# Windows
run-integration.bat

# Manual
./batch-integrate.sh -p projects -s attendance-app
```

## Files Overview

| File | Purpose |
|------|---------|
| `attendance-integration-script.php` | Core integration script that copies files and modifies code |
| `batch-integrate.sh` | Linux/Mac batch processor for multiple apps |
| `batch-integrate.bat` | Windows batch processor for multiple apps |
| `generate-config.php` | Interactive configuration generator |
| `ATTENDANCE_INTEGRATION_PLAN.md` | Detailed integration documentation |
| `INTEGRATION_QUICKSTART.md` | Quick integration guide |

## Project Structure Expected

```
projects/
├── attendance-app/          # Source attendance system
├── main-app/               # Target Laravel app
├── client-app/             # Another target app
└── other-apps/             # More target apps
```

## Usage Examples

### Basic Integration
```bash
# Integrate attendance system into all Laravel apps
./batch-integrate.sh
```

### Specific App Integration
```bash
# Integrate into specific app only
./batch-integrate.sh -t my-app
```

### Custom Configuration
```bash
# Use custom projects directory and source app
./batch-integrate.sh -p /var/www -s hr-system
```

### List Available Apps
```bash
# See what Laravel apps are available
./batch-integrate.sh -l
```

## Configuration File

The `integration-config.json` file controls integration behavior:

```json
{
    "exclude_apps": ["test-app", "backup-app"],
    "include_apps": [],
    "auto_migrate": true,
    "auto_sync_users": true,
    "backup_before_integration": true,
    "post_integration_commands": [
        "php artisan config:clear",
        "php artisan route:clear",
        "php artisan view:clear"
    ]
}
```

### Configuration Options

| Option | Type | Description |
|--------|------|-------------|
| `exclude_apps` | Array | Apps to skip during integration |
| `include_apps` | Array | If specified, only these apps will be processed |
| `auto_migrate` | Boolean | Run migrations automatically |
| `auto_sync_users` | Boolean | Sync existing users with attendance system |
| `backup_before_integration` | Boolean | Create backup before integration |
| `post_integration_commands` | Array | Commands to run after integration |

## What Gets Integrated

### Models
- Employee management with user synchronization
- Attendance tracking with GPS support
- Department and position hierarchy
- Leave and holiday management
- Multi-tenant data isolation

### Controllers
- Admin attendance dashboard
- Employee self-service interface
- Complete CRUD operations
- Reporting and analytics

### Views
- Responsive admin interface
- Employee attendance portal
- Real-time dashboards
- Mobile-friendly design

### Database
- Multi-tenant migrations
- User synchronization tables
- Attendance tracking schema
- Hierarchical organization structure

## Multi-Tenant Features

### Automatic Tenant Support
- Adds `tenant_id` columns to all tables
- Implements tenant scoping on models
- Isolates data between tenants
- Syncs with existing user base

### User Synchronization
- Maps existing users to employee records
- Maintains user permissions and roles
- Automatic sync for new users
- Preserves existing authentication

## Post-Integration Setup

### 1. Navigation Integration

**Admin Sidebar**
```blade
<li class="nav-item">
    <a href="{{ route('admin.attendance.dashboard') }}" class="nav-link">
        <i class="nav-icon fas fa-clock"></i>
        <p>Attendance Management</p>
    </a>
</li>
```

**Employee Navigation**
```blade
<li class="nav-item">
    <a href="{{ route('attendance.dashboard') }}" class="nav-link">
        <i class="nav-icon fas fa-clock"></i>
        <p>My Attendance</p>
    </a>
</li>
```

### 2. Permissions Setup
```bash
# Seed attendance permissions
php artisan db:seed --class=AttendancePermissionsSeeder
```

### 3. User Sync Command
```bash
# Sync existing users with attendance system
php artisan attendance:sync-users
```

## Available Routes

### Admin Routes
- `/admin/attendance` - Dashboard with statistics
- `/admin/attendance/employees` - Employee management
- `/admin/attendance/records` - Attendance records
- `/admin/attendance/reports` - Analytics and reports

### Employee Routes
- `/attendance` - Employee dashboard
- `/attendance/clock-in` - Clock in endpoint
- `/attendance/clock-out` - Clock out endpoint

## Features

### For Administrators
- **Dashboard**: Real-time attendance statistics
- **Employee Management**: Complete employee lifecycle
- **Attendance Monitoring**: Track all employee attendance
- **Reporting**: Comprehensive analytics and exports
- **Department Management**: Organizational hierarchy
- **Leave Management**: Vacation and leave tracking

### For Employees
- **Self-Service Portal**: Clock in/out interface
- **GPS Tracking**: Location-based attendance
- **Attendance History**: Personal attendance records
- **Leave Requests**: Request time off
- **Mobile Support**: Responsive design

## Troubleshooting

### Common Issues

**Integration Script Fails**
```bash
# Check PHP version (requires 7.4+)
php -v

# Verify Laravel installation
php artisan --version

# Check file permissions
chmod +x batch-integrate.sh
```

**Migration Errors**
```bash
# Check database connection
php artisan migrate:status

# Run migrations manually
php artisan migrate --force
```

**User Sync Issues**
```bash
# Check user table structure
php artisan tinker
>>> \App\Models\User::first()

# Manual sync
php artisan attendance:sync-users
```

### Rollback Process

**Restore from Backup**
```bash
# Backups are created with timestamp
mv /path/to/app /path/to/app_broken
mv /path/to/app_backup_YYYYMMDD_HHMMSS /path/to/app
```

**Remove Attendance Tables**
```bash
# Rollback migrations
php artisan migrate:rollback --step=10
```

## Security Considerations

- All attendance data is tenant-isolated
- GPS location tracking is optional
- User permissions are preserved
- Audit trails for all modifications
- Secure API endpoints

## Performance Optimization

- Database indexes on tenant columns
- Eager loading for relationships
- Pagination for large datasets
- Caching for frequently accessed data
- Background processing for heavy operations

## Support

### Requirements
- PHP 7.4 or higher
- Laravel 8.0 or higher
- MySQL 5.7+ or PostgreSQL 10+
- Multi-tenant package (optional but recommended)

### Dependencies Added
- `maatwebsite/excel` - Excel export functionality
- `stancl/tenancy` - Multi-tenant support (if not present)

### File Structure After Integration
```
app/
├── Models/Attendance/          # Attendance models
├── Http/Controllers/
│   ├── Admin/Attendance/       # Admin controllers
│   └── Employee/Attendance/    # Employee controllers
├── Services/Attendance/        # Business logic services
└── Providers/
    └── AttendanceServiceProvider.php

resources/views/attendance/     # All attendance views
database/migrations/attendance/ # Attendance migrations
public/attendance/             # Attendance assets
routes/attendance.php          # Attendance routes
config/attendance.php          # Attendance configuration
```

## License

This integration toolkit maintains the same license as your target Laravel applications.