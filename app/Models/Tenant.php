<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'database_name',
        'domain',
        'settings',
        'is_active',
        'trial_ends_at',
        'subscription_ends_at',
        'subscription_status'
    ];

    protected $casts = [
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function createDatabase()
    {
        // For SQLite, create a new database file
        $databasePath = database_path($this->database_name . '.sqlite');
        
        if (!file_exists($databasePath)) {
            touch($databasePath);
            
            // Set up the database connection for this tenant
            config(['database.connections.tenant' => [
                'driver' => 'sqlite',
                'database' => $databasePath,
                'prefix' => '',
                'foreign_key_constraints' => true,
            ]]);

            // Run migrations on the new database
            $this->runMigrations();
        }
    }

    private function runMigrations()
    {
        // Switch to tenant database connection
        DB::purge('tenant');
        config(['database.default' => 'tenant']);
        
        // Run basic attendance system migrations
        $this->createTenantTables();
        
        // Switch back to default connection
        config(['database.default' => 'sqlite']);
    }

    private function createTenantTables()
    {
        Schema::connection('tenant')->create('employees', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('card_number')->nullable();
            $table->string('nickname')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::connection('tenant')->create('attendance_records', function ($table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->timestamp('punch_time');
            $table->integer('punch_state'); // 0=check-in, 1=check-out, 2=break-out, 3=break-in
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('departments', function ($table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::connection('tenant')->create('positions', function ($table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function isTrialActive()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function isSubscriptionActive()
    {
        return $this->subscription_status === 'active' && 
               $this->subscription_ends_at && 
               $this->subscription_ends_at->isFuture();
    }
}