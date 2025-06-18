# Complete Usage Example

## Scenario: Multi-Tenant SaaS with Attendance Integration

### Your Project Structure
```
/var/www/
├── attendance-system/     # Source attendance app
├── main-saas/            # Primary SaaS application  
├── client-portal/        # Client-specific portal
└── mobile-api/          # Mobile API backend
```

### Step 1: Initial Setup
```bash
# Download integration scripts to your server
cd /var/www
wget https://your-server.com/attendance-integration-toolkit.zip
unzip attendance-integration-toolkit.zip

# Make scripts executable
chmod +x batch-integrate.sh
chmod +x *.sh
```

### Step 2: Generate Configuration
```bash
# Interactive configuration setup
php generate-config.php /var/www

# This will prompt you for:
# - Source app selection (attendance-system)
# - Apps to exclude (none in this case)
# - Migration preferences (yes)
# - User sync preferences (yes)
# - Backup preferences (yes)
```

### Step 3: Run Integration
```bash
# Option A: Integrate all apps
./batch-integrate.sh -p /var/www -s attendance-system

# Option B: Integrate specific apps only
./batch-integrate.sh -p /var/www -s attendance-system -t main-saas
./batch-integrate.sh -p /var/www -s attendance-system -t client-portal
```

### Step 4: Post Integration Setup

**For each integrated app:**
```bash
cd /var/www/main-saas

# Run migrations
php artisan migrate

# Sync existing users
php artisan attendance:sync-users

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Register service provider (if not auto-discovered)
# Add to config/app.php:
# App\Providers\AttendanceServiceProvider::class,
```

### Step 5: Update Navigation

**Admin Layout (resources/views/admin/layouts/sidebar.blade.php):**
```blade
<!-- Add to admin navigation -->
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-clock"></i>
        <p>
            Attendance
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('admin.attendance.dashboard') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Dashboard</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.attendance.employees') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Employees</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.attendance.records') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Records</p>
            </a>
        </li>
    </ul>
</li>
```

**Employee Navigation:**
```blade
<li class="nav-item">
    <a href="{{ route('attendance.dashboard') }}" class="nav-link">
        <i class="nav-icon fas fa-clock"></i>
        <p>My Attendance</p>
    </a>
</li>
```

### Step 6: Verify Integration

**Check Admin Dashboard:**
- Visit `/admin/attendance`
- Verify employee count matches user count
- Check that departments are listed

**Check Employee Interface:**
- Visit `/attendance`
- Test clock in/out functionality
- Verify GPS location capture

**Test User Sync:**
```bash
# Create a new user through your existing system
# Then check if employee record was created
php artisan tinker
>>> App\Models\User::latest()->first()
>>> App\Models\Attendance\Employee::latest()->first()
```

### Step 7: Customize for Your Needs

**Theme Integration:**
```bash
# Copy your existing admin theme styles
cp resources/sass/_variables.scss resources/views/attendance/admin/
cp resources/sass/app.scss resources/views/attendance/admin/

# Compile attendance assets
npm run dev
```

**Custom Business Rules:**
```php
// config/attendance.php
return [
    'late_threshold' => '09:30',  // Your company's late time
    'work_hours_per_day' => 8,
    'enable_gps' => true,
    'gps_radius' => 100,  // meters
    'auto_clock_out' => true,
    'auto_clock_out_time' => '18:00',
];
```

### Step 8: Advanced Configuration

**Multi-Tenant Isolation:**
```php
// Ensure tenant isolation in AppServiceProvider
public function boot()
{
    if (class_exists(\Stancl\Tenancy\Middleware\InitializeTenancy::class)) {
        // Tenancy is already configured
        $this->configureTenantScopes();
    }
}

private function configureTenantScopes()
{
    $models = [
        \App\Models\Attendance\Employee::class,
        \App\Models\Attendance\Attendance::class,
        \App\Models\Attendance\Department::class,
    ];

    foreach ($models as $model) {
        $model::addGlobalScope(new \Stancl\Tenancy\Database\TenantScope);
    }
}
```

**Permissions Integration:**
```php
// In your existing RoleSeeder
$attendancePermissions = [
    'attendance.view_dashboard',
    'attendance.manage_employees', 
    'attendance.view_records',
    'attendance.edit_records',
    'attendance.export_data',
];

foreach ($attendancePermissions as $permission) {
    Permission::firstOrCreate(['name' => $permission]);
}

// Assign to admin role
$adminRole->givePermissionTo($attendancePermissions);
```

### Expected Results

**What you'll have after integration:**

1. **Admin Dashboard** showing:
   - Total employees: 150
   - Present today: 142
   - Late arrivals: 8
   - Recent attendance activity

2. **Employee Portal** with:
   - Clock in/out buttons
   - Today's attendance summary
   - Monthly statistics
   - GPS location tracking

3. **Multi-Tenant Data**:
   - Each tenant sees only their data
   - Automatic user synchronization
   - Isolated attendance records

4. **Mobile-Friendly Interface**:
   - Responsive design
   - Touch-friendly buttons
   - GPS-based location tracking

### Troubleshooting Common Issues

**Migration Errors:**
```bash
# Check if tables already exist
php artisan migrate:status

# If conflicts, rollback and retry
php artisan migrate:rollback --step=5
php artisan migrate
```

**User Sync Issues:**
```bash
# Check user model structure
php artisan tinker
>>> \App\Models\User::first()->toArray()

# Manual employee creation
>>> $user = \App\Models\User::first();
>>> \App\Models\Attendance\Employee::syncFromUser($user);
```

**Permission Errors:**
```bash
# Ensure attendance routes are accessible
php artisan route:list | grep attendance

# Check middleware configuration
php artisan config:show auth
```

### Performance Optimization

**Database Optimization:**
```sql
-- Add indexes for better performance
CREATE INDEX idx_attendance_tenant_date ON attendances(tenant_id, punch_time);
CREATE INDEX idx_employee_tenant_active ON personnel_employee(tenant_id, status);
```

**Caching Strategy:**
```php
// Cache attendance statistics
Cache::remember("attendance_stats_{$tenantId}", 300, function() {
    return [
        'total_employees' => Employee::count(),
        'present_today' => Attendance::todayPresent()->count(),
        'departments' => Department::count(),
    ];
});
```

This complete example shows exactly how to integrate the attendance system into your existing multi-tenant Laravel applications with real-world configuration and customization.