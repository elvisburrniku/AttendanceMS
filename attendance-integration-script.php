<?php
#!/usr/bin/env php
<?php

/**
 * Attendance Management System Integration Script
 * Automatically integrates attendance system into multi-tenant Laravel applications
 * 
 * Usage: php attendance-integration-script.php --target=/path/to/target-app --source=/path/to/attendance-app
 */

class AttendanceIntegrator
{
    private $sourceDir;
    private $targetDir;
    private $config;
    private $log = [];

    public function __construct($sourceDir, $targetDir)
    {
        $this->sourceDir = rtrim($sourceDir, '/');
        $this->targetDir = rtrim($targetDir, '/');
        $this->config = $this->loadConfig();
    }

    private function loadConfig()
    {
        return [
            'models_to_copy' => [
                'Employee.php' => 'Attendance/Employee.php',
                'Attendance.php' => 'Attendance/Attendance.php',
                'Department.php' => 'Attendance/Department.php',
                'Position.php' => 'Attendance/Position.php',
                'Area.php' => 'Attendance/Area.php',
                'Holiday.php' => 'Attendance/Holiday.php',
                'Leave.php' => 'Attendance/Leave.php',
                'LeaveType.php' => 'Attendance/LeaveType.php',
                'Schedule.php' => 'Attendance/Schedule.php',
                'Shift.php' => 'Attendance/Shift.php',
            ],
            'controllers_to_copy' => [
                'EmployeeController.php' => 'Attendance/EmployeeController.php',
                'AttendanceController.php' => 'Attendance/AttendanceController.php',
                'DepartmentController.php' => 'Attendance/DepartmentController.php',
                'PositionController.php' => 'Attendance/PositionController.php',
                'HolidayController.php' => 'Attendance/HolidayController.php',
                'LeaveController.php' => 'Attendance/LeaveController.php',
                'ScheduleController.php' => 'Attendance/ScheduleController.php',
            ],
            'migrations_to_copy' => [
                '2019_11_25_113026_create_employees_table.php',
                '2019_12_02_141403_create_roles_table.php',
                '2019_12_03_044741_create_schedules_table.php',
                '2019_12_03_045452_create_attendances_table.php',
                '2024_04_26_012535_create_attendance_comments_table.php',
                '2024_07_20_162757_create_leave_types_table.php',
                '2024_08_24_122236_create_holidays_table.php',
            ],
            'view_directories' => [
                'admin' => 'attendance/admin',
                'layouts' => 'attendance/layouts',
            ],
            'asset_directories' => [
                'assets',
                'plugins',
                'css',
                'js',
            ]
        ];
    }

    public function integrate()
    {
        $this->log("Starting attendance system integration...");
        
        try {
            $this->validateDirectories();
            $this->createDirectoryStructure();
            $this->copyModels();
            $this->copyControllers();
            $this->copyMigrations();
            $this->copyViews();
            $this->copyAssets();
            $this->createIntegrationMigrations();
            $this->createServiceProvider();
            $this->createRoutes();
            $this->createCommands();
            $this->updateComposerJson();
            $this->createConfigFiles();
            $this->generateDocumentation();
            
            $this->log("Integration completed successfully!");
            $this->printInstructions();
            
        } catch (Exception $e) {
            $this->log("Error: " . $e->getMessage(), 'error');
            exit(1);
        }
    }

    private function validateDirectories()
    {
        if (!is_dir($this->sourceDir)) {
            throw new Exception("Source directory does not exist: {$this->sourceDir}");
        }
        
        if (!is_dir($this->targetDir)) {
            throw new Exception("Target directory does not exist: {$this->targetDir}");
        }
        
        if (!file_exists($this->targetDir . '/artisan')) {
            throw new Exception("Target directory is not a Laravel application: {$this->targetDir}");
        }
        
        $this->log("Directories validated successfully");
    }

    private function createDirectoryStructure()
    {
        $directories = [
            'app/Models/Attendance',
            'app/Http/Controllers/Admin/Attendance',
            'app/Http/Controllers/Employee/Attendance',
            'app/Services/Attendance',
            'database/migrations/attendance',
            'resources/views/attendance',
            'public/attendance',
            'config/attendance',
        ];

        foreach ($directories as $dir) {
            $fullPath = $this->targetDir . '/' . $dir;
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
                $this->log("Created directory: $dir");
            }
        }
    }

    private function copyModels()
    {
        $sourceModelsDir = $this->sourceDir . '/app/Models';
        $targetModelsDir = $this->targetDir . '/app/Models';

        foreach ($this->config['models_to_copy'] as $source => $target) {
            $sourcePath = $sourceModelsDir . '/' . $source;
            $targetPath = $targetModelsDir . '/' . $target;

            if (file_exists($sourcePath)) {
                $content = file_get_contents($sourcePath);
                $content = $this->updateModelContent($content, $source);
                
                $this->ensureDirectoryExists(dirname($targetPath));
                file_put_contents($targetPath, $content);
                $this->log("Copied model: $source -> $target");
            } else {
                $this->log("Model not found: $source", 'warning');
            }
        }
    }

    private function updateModelContent($content, $filename)
    {
        // Add tenant support
        $content = str_replace(
            'use Illuminate\Database\Eloquent\Model;',
            "use Illuminate\Database\Eloquent\Model;\nuse Stancl\Tenancy\Database\Concerns\BelongsToTenant;",
            $content
        );

        // Add trait usage
        $content = preg_replace(
            '/class\s+\w+\s+extends\s+Model\s*\{/',
            '$0' . "\n    use BelongsToTenant;",
            $content
        );

        // Update namespace for specific models
        if (strpos($filename, 'Employee') !== false) {
            $content = str_replace(
                'namespace App\Models;',
                'namespace App\Models\Attendance;',
                $content
            );

            // Add user sync method
            $syncMethod = '
    public static function syncFromUser($user)
    {
        return static::updateOrCreate(
            [\'user_id\' => $user->id],
            [
                \'emp_code\' => $user->id,
                \'first_name\' => $user->first_name ?? explode(\' \', $user->name)[0],
                \'last_name\' => $user->last_name ?? explode(\' \', $user->name)[1] ?? \'\',
                \'email\' => $user->email,
                \'mobile\' => $user->phone ?? \'\',
                \'hire_date\' => $user->created_at,
                \'gender\' => \'M\',
                \'birthday\' => now()->subYears(25),
                \'emp_type\' => 1,
                \'create_time\' => now(),
                \'status\' => 1,
            ]
        );
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, \'user_id\');
    }';

            $content = str_replace(
                '}' . PHP_EOL . PHP_EOL,
                $syncMethod . PHP_EOL . '}' . PHP_EOL . PHP_EOL,
                $content
            );
        }

        return $content;
    }

    private function copyControllers()
    {
        $sourceControllersDir = $this->sourceDir . '/app/Http/Controllers';
        $targetControllersDir = $this->targetDir . '/app/Http/Controllers';

        foreach ($this->config['controllers_to_copy'] as $source => $target) {
            $sourcePath = $sourceControllersDir . '/' . $source;
            $targetPath = $targetControllersDir . '/' . $target;

            if (file_exists($sourcePath)) {
                $content = file_get_contents($sourcePath);
                $content = $this->updateControllerContent($content, $source);
                
                $this->ensureDirectoryExists(dirname($targetPath));
                file_put_contents($targetPath, $content);
                $this->log("Copied controller: $source -> $target");
            } else {
                $this->log("Controller not found: $source", 'warning');
            }
        }

        // Create new admin and employee controllers
        $this->createAdminController();
        $this->createEmployeeController();
    }

    private function updateControllerContent($content, $filename)
    {
        // Update namespace
        $content = str_replace(
            'namespace App\Http\Controllers;',
            'namespace App\Http\Controllers\Attendance;',
            $content
        );

        // Update model imports
        $content = str_replace(
            'use App\Models\\',
            'use App\Models\Attendance\\',
            $content
        );

        return $content;
    }

    private function createAdminController()
    {
        $content = '<?php

namespace App\Http\Controllers\Admin\Attendance;

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
            \'total_employees\' => Employee::count(),
            \'present_today\' => Attendance::whereDate(\'punch_time\', today())
                ->where(\'punch_state\', \'0\')->distinct(\'emp_id\')->count(),
            \'total_departments\' => Department::count(),
            \'late_today\' => Attendance::whereDate(\'punch_time\', today())
                ->where(\'punch_state\', \'0\')
                ->whereTime(\'punch_time\', \'>\', \'09:00\')
                ->count(),
        ];
        
        $recent_attendance = Attendance::with(\'employee\')
            ->latest(\'punch_time\')
            ->take(10)
            ->get();
        
        return view(\'attendance.admin.dashboard\', compact(\'stats\', \'recent_attendance\'));
    }
    
    public function employees(Request $request)
    {
        $query = Employee::with([\'department\', \'position\', \'user\']);
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where(\'first_name\', \'like\', \'%\' . $request->search . \'%\')
                  ->orWhere(\'last_name\', \'like\', \'%\' . $request->search . \'%\')
                  ->orWhere(\'emp_code\', \'like\', \'%\' . $request->search . \'%\');
            });
        }
        
        $employees = $query->paginate(50);
        $departments = Department::all();
        
        return view(\'attendance.admin.employees\', compact(\'employees\', \'departments\'));
    }
    
    public function attendanceRecords(Request $request)
    {
        $query = Attendance::with(\'employee\');
        
        if ($request->date) {
            $query->whereDate(\'punch_time\', $request->date);
        } else {
            $query->whereDate(\'punch_time\', today());
        }
        
        if ($request->employee_id) {
            $query->where(\'emp_id\', $request->employee_id);
        }
        
        $attendances = $query->latest(\'punch_time\')->paginate(100);
        $employees = Employee::select(\'id\', \'first_name\', \'last_name\', \'emp_code\')->get();
        
        return view(\'attendance.admin.records\', compact(\'attendances\', \'employees\'));
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
            \'success\' => true,
            \'message\' => "Synced {$synced} users successfully"
        ]);
    }
}';

        file_put_contents(
            $this->targetDir . '/app/Http/Controllers/Admin/Attendance/AttendanceController.php',
            $content
        );

        $this->log("Created admin attendance controller");
    }

    private function createEmployeeController()
    {
        $content = '<?php

namespace App\Http\Controllers\Employee\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Employee;
use App\Models\Attendance\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function dashboard()
    {
        $employee = $this->getOrCreateEmployee();
        
        $today_attendance = Attendance::where(\'emp_id\', $employee->id)
            ->whereDate(\'punch_time\', today())
            ->orderBy(\'punch_time\')
            ->get();
        
        $this_month_stats = [
            \'total_days\' => Attendance::where(\'emp_id\', $employee->id)
                ->whereMonth(\'punch_time\', now()->month)
                ->whereYear(\'punch_time\', now()->year)
                ->where(\'punch_state\', \'0\')
                ->distinct(\'punch_time\')
                ->count(),
            \'late_days\' => Attendance::where(\'emp_id\', $employee->id)
                ->whereMonth(\'punch_time\', now()->month)
                ->whereYear(\'punch_time\', now()->year)
                ->where(\'punch_state\', \'0\')
                ->whereTime(\'punch_time\', \'>\', \'09:00\')
                ->count(),
        ];
        
        return view(\'attendance.employee.dashboard\', compact(\'employee\', \'today_attendance\', \'this_month_stats\'));
    }
    
    public function clockIn(Request $request)
    {
        $employee = $this->getOrCreateEmployee();
        
        // Check if already clocked in today
        $existing = Attendance::where(\'emp_id\', $employee->id)
            ->whereDate(\'punch_time\', today())
            ->where(\'punch_state\', \'0\')
            ->first();
            
        if ($existing) {
            return response()->json([\'error\' => \'Already clocked in today\'], 400);
        }
        
        Attendance::create([
            \'emp_id\' => $employee->id,
            \'emp_code\' => $employee->emp_code,
            \'punch_time\' => now(),
            \'punch_state\' => \'0\',
            \'latitude\' => $request->latitude,
            \'longitude\' => $request->longitude,
            \'gps_location\' => $request->has(\'latitude\'),
            \'source\' => \'web\',
            \'mobile\' => 1,
        ]);
        
        return response()->json([\'success\' => true, \'message\' => \'Clocked in successfully\']);
    }
    
    public function clockOut(Request $request)
    {
        $employee = $this->getOrCreateEmployee();
        
        // Check if clocked in today
        $checkin = Attendance::where(\'emp_id\', $employee->id)
            ->whereDate(\'punch_time\', today())
            ->where(\'punch_state\', \'0\')
            ->first();
            
        if (!$checkin) {
            return response()->json([\'error\' => \'Must clock in first\'], 400);
        }
        
        // Check if already clocked out
        $checkout = Attendance::where(\'emp_id\', $employee->id)
            ->whereDate(\'punch_time\', today())
            ->where(\'punch_state\', \'1\')
            ->first();
            
        if ($checkout) {
            return response()->json([\'error\' => \'Already clocked out today\'], 400);
        }
        
        Attendance::create([
            \'emp_id\' => $employee->id,
            \'emp_code\' => $employee->emp_code,
            \'punch_time\' => now(),
            \'punch_state\' => \'1\',
            \'latitude\' => $request->latitude,
            \'longitude\' => $request->longitude,
            \'gps_location\' => $request->has(\'latitude\'),
            \'source\' => \'web\',
            \'mobile\' => 1,
        ]);
        
        return response()->json([\'success\' => true, \'message\' => \'Clocked out successfully\']);
    }
    
    private function getOrCreateEmployee()
    {
        $employee = Employee::where(\'user_id\', auth()->id())->first();
        
        if (!$employee) {
            $employee = Employee::syncFromUser(auth()->user());
        }
        
        return $employee;
    }
}';

        file_put_contents(
            $this->targetDir . '/app/Http/Controllers/Employee/Attendance/AttendanceController.php',
            $content
        );

        $this->log("Created employee attendance controller");
    }

    private function copyMigrations()
    {
        $sourceMigrationsDir = $this->sourceDir . '/database/migrations';
        $targetMigrationsDir = $this->targetDir . '/database/migrations/attendance';

        foreach ($this->config['migrations_to_copy'] as $migration) {
            $sourcePath = $sourceMigrationsDir . '/' . $migration;
            $targetPath = $targetMigrationsDir . '/' . $migration;

            if (file_exists($sourcePath)) {
                copy($sourcePath, $targetPath);
                $this->log("Copied migration: $migration");
            } else {
                $this->log("Migration not found: $migration", 'warning');
            }
        }
    }

    private function copyViews()
    {
        $sourceViewsDir = $this->sourceDir . '/resources/views';
        $targetViewsDir = $this->targetDir . '/resources/views';

        foreach ($this->config['view_directories'] as $source => $target) {
            $sourcePath = $sourceViewsDir . '/' . $source;
            $targetPath = $targetViewsDir . '/' . $target;

            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $targetPath);
                $this->log("Copied views: $source -> $target");
            } else {
                $this->log("View directory not found: $source", 'warning');
            }
        }

        // Create basic views
        $this->createBasicViews();
    }

    private function copyAssets()
    {
        $sourcePublicDir = $this->sourceDir . '/public';
        $targetPublicDir = $this->targetDir . '/public/attendance';

        foreach ($this->config['asset_directories'] as $dir) {
            $sourcePath = $sourcePublicDir . '/' . $dir;
            $targetPath = $targetPublicDir . '/' . $dir;

            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $targetPath);
                $this->log("Copied assets: $dir");
            } else {
                $this->log("Asset directory not found: $dir", 'warning');
            }
        }
    }

    private function createIntegrationMigrations()
    {
        $timestamp = date('Y_m_d_His');
        
        // Tenant support migration
        $tenantMigration = "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTenantToAttendanceTables extends Migration
{
    public function up()
    {
        \$tables = [
            'personnel_employee', 'attendances', 'personnel_department',
            'personnel_position', 'personnel_area', 'att_attschedule',
            'holidays', 'leaves', 'employee_overtimes', 'leave_types'
        ];
        
        foreach (\$tables as \$table) {
            if (Schema::hasTable(\$table)) {
                Schema::table(\$table, function (Blueprint \$table) {
                    \$table->string('tenant_id')->after('id')->index();
                });
            }
        }
    }
    
    public function down()
    {
        \$tables = [
            'personnel_employee', 'attendances', 'personnel_department',
            'personnel_position', 'personnel_area', 'att_attschedule',
            'holidays', 'leaves', 'employee_overtimes', 'leave_types'
        ];
        
        foreach (\$tables as \$table) {
            if (Schema::hasTable(\$table)) {
                Schema::table(\$table, function (Blueprint \$table) {
                    \$table->dropColumn('tenant_id');
                });
            }
        }
    }
}";

        file_put_contents(
            $this->targetDir . '/database/migrations/' . $timestamp . '_add_tenant_to_attendance_tables.php',
            $tenantMigration
        );

        // User sync migration
        $syncMigration = "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEmployeeSync extends Migration
{
    public function up()
    {
        Schema::create('user_employee_sync', function (Blueprint \$table) {
            \$table->id();
            \$table->string('tenant_id')->index();
            \$table->unsignedBigInteger('user_id');
            \$table->unsignedBigInteger('employee_id');
            \$table->timestamps();
            
            \$table->unique(['tenant_id', 'user_id']);
            \$table->index(['tenant_id', 'employee_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('user_employee_sync');
    }
}";

        $timestamp2 = date('Y_m_d_His', time() + 1);
        file_put_contents(
            $this->targetDir . '/database/migrations/' . $timestamp2 . '_create_user_employee_sync.php',
            $syncMigration
        );

        $this->log("Created integration migrations");
    }

    private function createServiceProvider()
    {
        $content = '<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;

class AttendanceServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        User::observe(UserObserver::class);
        
        $this->loadRoutesFrom(__DIR__.\'/../routes/attendance.php\');
        $this->loadViewsFrom(resource_path(\'views/attendance\'), \'attendance\');
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\SyncAttendanceUsers::class,
            ]);
        }
    }
}';

        file_put_contents(
            $this->targetDir . '/app/Providers/AttendanceServiceProvider.php',
            $content
        );

        $this->log("Created attendance service provider");
    }

    private function createRoutes()
    {
        $adminRoutes = '<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Attendance\AttendanceController;

Route::middleware([\'auth\', \'admin\'])->prefix(\'admin/attendance\')->name(\'admin.attendance.\')->group(function () {
    Route::get(\'/\', [AttendanceController::class, \'dashboard\'])->name(\'dashboard\');
    Route::get(\'/employees\', [AttendanceController::class, \'employees\'])->name(\'employees\');
    Route::get(\'/records\', [AttendanceController::class, \'attendanceRecords\'])->name(\'records\');
    Route::post(\'/sync-users\', [AttendanceController::class, \'syncUsers\'])->name(\'sync-users\');
});';

        $employeeRoutes = '
Route::middleware([\'auth\'])->prefix(\'attendance\')->name(\'attendance.\')->group(function () {
    Route::get(\'/\', [\App\Http\Controllers\Employee\Attendance\AttendanceController::class, \'dashboard\'])->name(\'dashboard\');
    Route::post(\'/clock-in\', [\App\Http\Controllers\Employee\Attendance\AttendanceController::class, \'clockIn\'])->name(\'clock-in\');
    Route::post(\'/clock-out\', [\App\Http\Controllers\Employee\Attendance\AttendanceController::class, \'clockOut\'])->name(\'clock-out\');
});';

        file_put_contents(
            $this->targetDir . '/routes/attendance.php',
            "<?php\n\n" . $adminRoutes . "\n" . $employeeRoutes
        );

        $this->log("Created attendance routes");
    }

    private function createCommands()
    {
        $content = '<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance\Employee;

class SyncAttendanceUsers extends Command
{
    protected $signature = \'attendance:sync-users\';
    protected $description = \'Sync existing users with attendance employees\';

    public function handle()
    {
        $users = User::all();
        $synced = 0;

        $this->info(\'Starting user sync...\');

        foreach ($users as $user) {
            Employee::syncFromUser($user);
            $synced++;
            $this->info("Synced user: {$user->name}");
        }

        $this->info("Successfully synced {$synced} users.");
    }
}';

        $this->ensureDirectoryExists($this->targetDir . '/app/Console/Commands');
        file_put_contents(
            $this->targetDir . '/app/Console/Commands/SyncAttendanceUsers.php',
            $content
        );

        $this->log("Created sync command");
    }

    private function createBasicViews()
    {
        // Admin dashboard view
        $adminDashboard = '@extends(\'admin.layouts.app\')

@section(\'content\')
<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Employees</h5>
                <h2>{{ $stats[\'total_employees\'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Present Today</h5>
                <h2>{{ $stats[\'present_today\'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Departments</h5>
                <h2>{{ $stats[\'total_departments\'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Late Today</h5>
                <h2>{{ $stats[\'late_today\'] }}</h2>
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
                            <td>{{ $attendance->punch_time->format(\'Y-m-d H:i:s\') }}</td>
                            <td>
                                <span class="badge badge-{{ $attendance->punch_state == \'0\' ? \'success\' : \'danger\' }}">
                                    {{ $attendance->punch_state == \'0\' ? \'Check In\' : \'Check Out\' }}
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
@endsection';

        $this->ensureDirectoryExists($this->targetDir . '/resources/views/attendance/admin');
        file_put_contents(
            $this->targetDir . '/resources/views/attendance/admin/dashboard.blade.php',
            $adminDashboard
        );

        // Employee dashboard view
        $employeeDashboard = '@extends(\'layouts.app\')

@section(\'content\')
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
                            $checkedIn = $today_attendance->where(\'punch_state\', \'0\')->first();
                            $checkedOut = $today_attendance->where(\'punch_state\', \'1\')->first();
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
                        <h5>Today\'s Activity</h5>
                        <div class="row">
                            @foreach($today_attendance as $attendance)
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>{{ $attendance->punch_state == \'0\' ? \'Check In\' : \'Check Out\' }}</h6>
                                        <p>{{ $attendance->punch_time->format(\'H:i:s\') }}</p>
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
            submitAttendance(\'/attendance/clock-in\', {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            });
        }, function() {
            submitAttendance(\'/attendance/clock-in\', {});
        });
    } else {
        submitAttendance(\'/attendance/clock-in\', {});
    }
}

function clockOut() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            submitAttendance(\'/attendance/clock-out\', {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            });
        }, function() {
            submitAttendance(\'/attendance/clock-out\', {});
        });
    } else {
        submitAttendance(\'/attendance/clock-out\', {});
    }
}

function submitAttendance(url, data) {
    fetch(url, {
        method: \'POST\',
        headers: {
            \'Content-Type\': \'application/json\',
            \'X-CSRF-TOKEN\': document.querySelector(\'meta[name="csrf-token"]\').getAttribute(\'content\')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || \'An error occurred\');
        }
    })
    .catch(error => {
        console.error(\'Error:\', error);
        alert(\'An error occurred\');
    });
}
</script>
@endsection';

        $this->ensureDirectoryExists($this->targetDir . '/resources/views/attendance/employee');
        file_put_contents(
            $this->targetDir . '/resources/views/attendance/employee/dashboard.blade.php',
            $employeeDashboard
        );

        $this->log("Created basic views");
    }

    private function updateComposerJson()
    {
        $composerPath = $this->targetDir . '/composer.json';
        if (file_exists($composerPath)) {
            $composer = json_decode(file_get_contents($composerPath), true);
            
            // Add service provider
            if (!isset($composer['extra']['laravel']['providers'])) {
                $composer['extra']['laravel']['providers'] = [];
            }
            
            if (!in_array('App\\Providers\\AttendanceServiceProvider', $composer['extra']['laravel']['providers'])) {
                $composer['extra']['laravel']['providers'][] = 'App\\Providers\\AttendanceServiceProvider';
            }
            
            file_put_contents($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->log("Updated composer.json");
        }
    }

    private function createConfigFiles()
    {
        $config = '<?php

return [
    \'default_work_hours\' => 8,
    \'late_threshold\' => \'09:00\',
    \'enable_gps\' => true,
    \'auto_sync_users\' => true,
    \'attendance_states\' => [
        \'0\' => \'Check In\',
        \'1\' => \'Check Out\',
        \'2\' => \'Break Out\',
        \'3\' => \'Break In\',
    ],
];';

        file_put_contents($this->targetDir . '/config/attendance.php', $config);
        $this->log("Created config file");
    }

    private function generateDocumentation()
    {
        $readme = '# Attendance Management System Integration

## Overview
This attendance management system has been successfully integrated into your Laravel application.

## Features
- Multi-tenant support
- User synchronization
- GPS location tracking
- Admin dashboard
- Employee self-service
- Reporting and analytics

## Usage

### For Administrators
Access the admin dashboard at: `/admin/attendance`

### For Employees
Access the employee dashboard at: `/attendance`

## Commands

### Sync Users
```bash
php artisan attendance:sync-users
```

### Run Migrations
```bash
php artisan migrate
```

## Configuration
Edit `config/attendance.php` to customize settings.

## Support
For support and customization, refer to the original documentation.
';

        file_put_contents($this->targetDir . '/ATTENDANCE_README.md', $readme);
        $this->log("Generated documentation");
    }

    private function copyDirectory($src, $dst)
    {
        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }

        $files = scandir($src);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $srcFile = $src . '/' . $file;
                $dstFile = $dst . '/' . $file;

                if (is_dir($srcFile)) {
                    $this->copyDirectory($srcFile, $dstFile);
                } else {
                    copy($srcFile, $dstFile);
                }
            }
        }
    }

    private function ensureDirectoryExists($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    private function log($message, $type = 'info')
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$type] $message";
        $this->log[] = $logEntry;
        
        $color = $type === 'error' ? "\033[31m" : ($type === 'warning' ? "\033[33m" : "\033[32m");
        echo $color . $logEntry . "\033[0m\n";
    }

    private function printInstructions()
    {
        echo "\n\033[1;32m=== INTEGRATION COMPLETED SUCCESSFULLY ===\033[0m\n\n";
        echo "Next steps:\n";
        echo "1. Run migrations: \033[33mphp artisan migrate\033[0m\n";
        echo "2. Sync users: \033[33mphp artisan attendance:sync-users\033[0m\n";
        echo "3. Register service provider in config/app.php if not auto-discovered\n";
        echo "4. Add navigation links to your admin and employee dashboards\n";
        echo "5. Customize views to match your application theme\n\n";
        echo "Admin Dashboard: \033[36m/admin/attendance\033[0m\n";
        echo "Employee Dashboard: \033[36m/attendance\033[0m\n\n";
        echo "Documentation: \033[36mATTENDANCE_README.md\033[0m\n";
    }
}

// Command line interface
function showUsage()
{
    echo "Attendance Management System Integration Script\n\n";
    echo "Usage:\n";
    echo "  php attendance-integration-script.php --source=SOURCE_DIR --target=TARGET_DIR\n\n";
    echo "Options:\n";
    echo "  --source=DIR    Source attendance system directory\n";
    echo "  --target=DIR    Target Laravel application directory\n";
    echo "  --help          Show this help message\n\n";
    echo "Examples:\n";
    echo "  php attendance-integration-script.php --source=./attendance-app --target=./my-app\n";
    echo "  php attendance-integration-script.php --source=/projects/attendance --target=/projects/main-app\n\n";
}

// Parse command line arguments
$options = getopt('', ['source:', 'target:', 'help']);

if (isset($options['help']) || empty($options['source']) || empty($options['target'])) {
    showUsage();
    exit(0);
}

$sourceDir = $options['source'];
$targetDir = $options['target'];

try {
    $integrator = new AttendanceIntegrator($sourceDir, $targetDir);
    $integrator->integrate();
} catch (Exception $e) {
    echo "\033[31mError: " . $e->getMessage() . "\033[0m\n";
    exit(1);
}