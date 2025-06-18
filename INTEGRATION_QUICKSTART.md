# Quick Start Integration Guide
## For Multi-Tenant Laravel Application

### Step 1: Copy Core Files (5 minutes)

**Models to Copy:**
```bash
# Copy these models to your app/Models/Attendance/ directory
cp app/Models/Employee.php → app/Models/Attendance/Employee.php
cp app/Models/Attendance.php → app/Models/Attendance/Attendance.php
cp app/Models/Department.php → app/Models/Attendance/Department.php
cp app/Models/Position.php → app/Models/Attendance/Position.php
cp app/Models/Area.php → app/Models/Attendance/Area.php
```

**Controllers to Copy:**
```bash
# Copy to app/Http/Controllers/Attendance/
cp app/Http/Controllers/EmployeeController.php → app/Http/Controllers/Attendance/EmployeeController.php
cp app/Http/Controllers/AttendanceController.php → app/Http/Controllers/Attendance/AttendanceController.php
cp app/Http/Controllers/DepartmentController.php → app/Http/Controllers/Attendance/DepartmentController.php
```

**Views to Copy:**
```bash
# Copy entire views structure
cp -r resources/views/admin → resources/views/attendance/admin
cp -r resources/views/layouts → resources/views/attendance/layouts
```

### Step 2: Database Integration (10 minutes)

**Migration 1: Add Tenant Columns**
```php
<?php
// database/migrations/xxxx_add_tenant_to_attendance_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTenantToAttendanceTables extends Migration
{
    public function up()
    {
        $tables = [
            'personnel_employee', 'attendances', 'personnel_department',
            'personnel_position', 'personnel_area', 'att_attschedule',
            'holidays', 'leaves', 'employee_overtimes', 'leave_types'
        ];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->string('tenant_id')->after('id')->index();
                });
            }
        }
    }
    
    public function down()
    {
        $tables = [
            'personnel_employee', 'attendances', 'personnel_department',
            'personnel_position', 'personnel_area', 'att_attschedule',
            'holidays', 'leaves', 'employee_overtimes', 'leave_types'
        ];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
}
```

**Migration 2: User-Employee Sync**
```php
<?php
// database/migrations/xxxx_create_user_employee_sync.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEmployeeSync extends Migration
{
    public function up()
    {
        Schema::create('user_employee_sync', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('employee_id');
            $table->timestamps();
            
            $table->unique(['tenant_id', 'user_id']);
            $table->index(['tenant_id', 'employee_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('user_employee_sync');
    }
}
```

### Step 3: Model Updates (15 minutes)

**Updated Employee Model:**
```php
<?php
// app/Models/Attendance/Employee.php
namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Employee extends Model
{
    use BelongsToTenant;
    
    protected $table = 'personnel_employee';
    protected $guarded = [];
    public $timestamps = false;
    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
    
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'emp_id');
    }
    
    public static function syncFromUser($user)
    {
        return static::updateOrCreate(
            ['user_id' => $user->id],
            [
                'emp_code' => $user->id,
                'first_name' => $user->first_name ?? explode(' ', $user->name)[0],
                'last_name' => $user->last_name ?? explode(' ', $user->name)[1] ?? '',
                'email' => $user->email,
                'mobile' => $user->phone ?? '',
                'hire_date' => $user->created_at,
                'gender' => 'M',
                'birthday' => now()->subYears(25),
                'emp_type' => 1,
                'create_time' => now(),
                'status' => 1,
            ]
        );
    }
}
```

**Updated Attendance Model:**
```php
<?php
// app/Models/Attendance/Attendance.php
namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Attendance extends Model
{
    use BelongsToTenant;
    
    protected $table = 'attendances';
    protected $guarded = [];
    public $timestamps = false;
    
    protected $dates = ['punch_time'];
    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }
    
    public function getPunchStateTextAttribute()
    {
        $states = [
            '0' => 'Check In',
            '1' => 'Check Out',
            '2' => 'Break Out',
            '3' => 'Break In',
        ];
        
        return $states[$this->punch_state] ?? 'Unknown';
    }
}
```

### Step 4: Controller Integration (20 minutes)

**Admin Controller:**
```php
<?php
// app/Http/Controllers/Admin/AttendanceController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Employee;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\Department;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_employees' => Employee::count(),
            'present_today' => Attendance::whereDate('punch_time', today())
                ->where('punch_state', '0')->distinct('emp_id')->count(),
            'total_departments' => Department::count(),
            'late_today' => Attendance::whereDate('punch_time', today())
                ->where('punch_state', '0')
                ->whereTime('punch_time', '>', '09:00')
                ->count(),
        ];
        
        $recent_attendance = Attendance::with('employee')
            ->latest('punch_time')
            ->take(10)
            ->get();
        
        return view('attendance.admin.dashboard', compact('stats', 'recent_attendance'));
    }
    
    public function employees(Request $request)
    {
        $query = Employee::with(['department', 'position', 'user']);
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('emp_code', 'like', '%' . $request->search . '%');
            });
        }
        
        $employees = $query->paginate(50);
        $departments = Department::all();
        
        return view('attendance.admin.employees', compact('employees', 'departments'));
    }
    
    public function attendanceRecords(Request $request)
    {
        $query = Attendance::with('employee');
        
        if ($request->date) {
            $query->whereDate('punch_time', $request->date);
        } else {
            $query->whereDate('punch_time', today());
        }
        
        if ($request->employee_id) {
            $query->where('emp_id', $request->employee_id);
        }
        
        $attendances = $query->latest('punch_time')->paginate(100);
        $employees = Employee::select('id', 'first_name', 'last_name', 'emp_code')->get();
        
        return view('attendance.admin.records', compact('attendances', 'employees'));
    }
    
    public function syncUsers()
    {
        $users = \App\Models\User::all();
        $synced = 0;
        
        foreach ($users as $user) {
            Employee::syncFromUser($user);
            $synced++;
        }
        
        return response()->json([
            'success' => true,
            'message' => "Synced {$synced} users successfully"
        ]);
    }
}
```

**Employee Controller:**
```php
<?php
// app/Http/Controllers/Employee/AttendanceController.php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Employee;
use App\Models\Attendance\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function dashboard()
    {
        $employee = $this->getOrCreateEmployee();
        
        $today_attendance = Attendance::where('emp_id', $employee->id)
            ->whereDate('punch_time', today())
            ->orderBy('punch_time')
            ->get();
        
        $this_month_stats = [
            'total_days' => Attendance::where('emp_id', $employee->id)
                ->whereMonth('punch_time', now()->month)
                ->whereYear('punch_time', now()->year)
                ->where('punch_state', '0')
                ->distinct('punch_time')
                ->count(),
            'late_days' => Attendance::where('emp_id', $employee->id)
                ->whereMonth('punch_time', now()->month)
                ->whereYear('punch_time', now()->year)
                ->where('punch_state', '0')
                ->whereTime('punch_time', '>', '09:00')
                ->count(),
        ];
        
        return view('attendance.employee.dashboard', compact('employee', 'today_attendance', 'this_month_stats'));
    }
    
    public function clockIn(Request $request)
    {
        $employee = $this->getOrCreateEmployee();
        
        // Check if already clocked in today
        $existing = Attendance::where('emp_id', $employee->id)
            ->whereDate('punch_time', today())
            ->where('punch_state', '0')
            ->first();
            
        if ($existing) {
            return response()->json(['error' => 'Already clocked in today'], 400);
        }
        
        Attendance::create([
            'emp_id' => $employee->id,
            'emp_code' => $employee->emp_code,
            'punch_time' => now(),
            'punch_state' => '0',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'gps_location' => $request->has('latitude'),
            'source' => 'web',
            'mobile' => 1,
        ]);
        
        return response()->json(['success' => true, 'message' => 'Clocked in successfully']);
    }
    
    public function clockOut(Request $request)
    {
        $employee = $this->getOrCreateEmployee();
        
        // Check if clocked in today
        $checkin = Attendance::where('emp_id', $employee->id)
            ->whereDate('punch_time', today())
            ->where('punch_state', '0')
            ->first();
            
        if (!$checkin) {
            return response()->json(['error' => 'Must clock in first'], 400);
        }
        
        // Check if already clocked out
        $checkout = Attendance::where('emp_id', $employee->id)
            ->whereDate('punch_time', today())
            ->where('punch_state', '1')
            ->first();
            
        if ($checkout) {
            return response()->json(['error' => 'Already clocked out today'], 400);
        }
        
        Attendance::create([
            'emp_id' => $employee->id,
            'emp_code' => $employee->emp_code,
            'punch_time' => now(),
            'punch_state' => '1',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'gps_location' => $request->has('latitude'),
            'source' => 'web',
            'mobile' => 1,
        ]);
        
        return response()->json(['success' => true, 'message' => 'Clocked out successfully']);
    }
    
    private function getOrCreateEmployee()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        
        if (!$employee) {
            $employee = Employee::syncFromUser(auth()->user());
        }
        
        return $employee;
    }
}
```

### Step 5: Route Integration (5 minutes)

**Admin Routes:**
```php
// In your admin routes file (routes/admin.php or similar)
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AttendanceController::class, 'dashboard'])->name('dashboard');
    Route::get('/employees', [\App\Http\Controllers\Admin\AttendanceController::class, 'employees'])->name('employees');
    Route::get('/records', [\App\Http\Controllers\Admin\AttendanceController::class, 'attendanceRecords'])->name('records');
    Route::post('/sync-users', [\App\Http\Controllers\Admin\AttendanceController::class, 'syncUsers'])->name('sync-users');
});
```

**Employee Routes:**
```php
// In your employee routes file
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Employee\AttendanceController::class, 'dashboard'])->name('dashboard');
    Route::post('/clock-in', [\App\Http\Controllers\Employee\AttendanceController::class, 'clockIn'])->name('clock-in');
    Route::post('/clock-out', [\App\Http\Controllers\Employee\AttendanceController::class, 'clockOut'])->name('clock-out');
});
```

### Step 6: Basic Views (10 minutes)

**Admin Dashboard View:**
```blade
{{-- resources/views/attendance/admin/dashboard.blade.php --}}
@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Employees</h5>
                <h2>{{ $stats['total_employees'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Present Today</h5>
                <h2>{{ $stats['present_today'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Departments</h5>
                <h2>{{ $stats['total_departments'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Late Today</h5>
                <h2>{{ $stats['late_today'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>Recent Attendance</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_attendance as $attendance)
                        <tr>
                            <td>{{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}</td>
                            <td>{{ $attendance->punch_time->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <span class="badge badge-{{ $attendance->punch_state == '0' ? 'success' : 'danger' }}">
                                    {{ $attendance->punch_state_text }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
```

**Employee Dashboard View:**
```blade
{{-- resources/views/attendance/employee/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3>Attendance Dashboard</h3>
                </div>
                <div class="card-body text-center">
                    <h4>Welcome, {{ $employee->first_name }}!</h4>
                    
                    <div class="mt-4">
                        @php
                            $checkedIn = $today_attendance->where('punch_state', '0')->first();
                            $checkedOut = $today_attendance->where('punch_state', '1')->first();
                        @endphp
                        
                        @if(!$checkedIn)
                            <button class="btn btn-success btn-lg" onclick="clockIn()">Clock In</button>
                        @elseif(!$checkedOut)
                            <button class="btn btn-danger btn-lg" onclick="clockOut()">Clock Out</button>
                        @else
                            <div class="alert alert-info">
                                You have completed your attendance for today!
                            </div>
                        @endif
                    </div>
                    
                    @if($today_attendance->count() > 0)
                    <div class="mt-4">
                        <h5>Today's Activity</h5>
                        <div class="row">
                            @foreach($today_attendance as $attendance)
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>{{ $attendance->punch_state_text }}</h6>
                                        <p>{{ $attendance->punch_time->format('H:i:s') }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clockIn() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            submitAttendance('/attendance/clock-in', {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            });
        }, function() {
            submitAttendance('/attendance/clock-in', {});
        });
    } else {
        submitAttendance('/attendance/clock-in', {});
    }
}

function clockOut() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            submitAttendance('/attendance/clock-out', {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            });
        }, function() {
            submitAttendance('/attendance/clock-out', {});
        });
    } else {
        submitAttendance('/attendance/clock-out', {});
    }
}

function submitAttendance(url, data) {
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}
</script>
@endsection
```

### Step 7: Run Integration (2 minutes)

```bash
# Run migrations
php artisan migrate

# Sync existing users
php artisan tinker
>>> App\Models\User::all()->each(function($user) { 
    App\Models\Attendance\Employee::syncFromUser($user); 
});

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 8: Add to Navigation

**Admin Sidebar Addition:**
```blade
<li class="nav-item">
    <a href="{{ route('admin.attendance.dashboard') }}" class="nav-link">
        <i class="nav-icon fas fa-clock"></i>
        <p>Attendance</p>
    </a>
</li>
```

**Employee Navigation Addition:**
```blade
<li class="nav-item">
    <a href="{{ route('attendance.dashboard') }}" class="nav-link">
        <i class="nav-icon fas fa-clock"></i>
        <p>My Attendance</p>
    </a>
</li>
```

## Total Integration Time: ~60 minutes

This quickstart guide provides the essential components to get attendance management working in your multi-tenant Laravel application. The system will automatically sync with your existing users and provide both admin and employee interfaces.

## What You Get:
- ✅ Multi-tenant attendance tracking
- ✅ Automatic user synchronization
- ✅ Admin dashboard with statistics
- ✅ Employee clock in/out interface
- ✅ GPS location tracking (optional)
- ✅ Attendance history and reporting
- ✅ Tenant data isolation
- ✅ Seamless integration with existing users

## Next Steps:
1. Customize the views to match your application's theme
2. Add more detailed reporting features
3. Implement leave management
4. Add department and position management
5. Configure email notifications
6. Add mobile-responsive features