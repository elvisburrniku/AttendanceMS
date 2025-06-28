<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = auth()->user()->tenants()->paginate(10);
        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);

        $slug = Str::slug($request->name) . '-' . Str::random(8);
        $databaseName = 'tenant_' . $slug;

        // Create tenant
        $tenant = Tenant::create([
            'name' => $request->name,
            'slug' => $slug,
            'database_name' => $databaseName,
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trial'
        ]);

        // Create tenant database
        $tenant->createDatabase();

        // Create admin user for this tenant
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'tenant_id' => $tenant->id,
            'role' => 'admin'
        ]);

        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'System created successfully! Trial period: 14 days');
    }

    public function show(Tenant $tenant)
    {
        // Check if user has access to this tenant
        if (auth()->user()->tenant_id !== $tenant->id && !auth()->user()->is_super_admin) {
            abort(403, 'Access denied');
        }

        $stats = $this->getTenantStats($tenant);
        return view('tenants.show', compact('tenant', 'stats'));
    }

    public function switch(Tenant $tenant)
    {
        // Switch user's active tenant
        session(['active_tenant_id' => $tenant->id]);
        
        return redirect()->route('dashboard')
            ->with('success', 'Switched to ' . $tenant->name);
    }

    private function getTenantStats($tenant)
    {
        // Switch to tenant database to get stats
        config(['database.connections.tenant.database' => database_path($tenant->database_name . '.sqlite')]);
        
        try {
            $employeeCount = \DB::connection('tenant')->table('employees')->count();
            $todayAttendance = \DB::connection('tenant')->table('attendance_records')
                ->whereDate('punch_time', today())->count();
            $departmentCount = \DB::connection('tenant')->table('departments')->count();
            $positionCount = \DB::connection('tenant')->table('positions')->count();
        } catch (\Exception $e) {
            // Fallback if tables don't exist yet
            $employeeCount = 0;
            $todayAttendance = 0;
            $departmentCount = 0;
            $positionCount = 0;
        }

        return [
            'employees' => $employeeCount,
            'today_attendance' => $todayAttendance,
            'departments' => $departmentCount,
            'positions' => $positionCount,
            'trial_days_left' => $tenant->trial_ends_at ? max(0, $tenant->trial_ends_at->diffInDays(now())) : 0
        ];
    }
}