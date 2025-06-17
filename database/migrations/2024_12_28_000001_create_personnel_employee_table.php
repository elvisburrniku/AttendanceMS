
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelEmployeeTable extends Migration
{
    public function up()
    {
        Schema::create('personnel_employee', function (Blueprint $table) {
            $table->id();
            $table->dateTime('create_time', 6)->nullable();
            $table->string('create_user', 150)->nullable();
            $table->dateTime('change_time', 6)->nullable();
            $table->string('change_user', 150)->nullable();
            $table->smallInteger('status');
            $table->string('emp_code', 20)->index();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('nickname', 100)->nullable();
            $table->string('passport', 30)->nullable();
            $table->string('driver_license_automobile', 30)->nullable();
            $table->string('driver_license_motorcycle', 30)->nullable();
            $table->string('photo', 200)->nullable();
            $table->string('self_password', 128)->nullable();
            $table->string('device_password', 20)->nullable();
            $table->integer('dev_privilege')->nullable();
            $table->string('card_no', 20)->nullable();
            $table->string('acc_group', 5)->nullable();
            $table->string('acc_timezone', 20)->nullable();
            $table->string('gender', 1)->nullable();
            $table->date('birthday')->nullable();
            $table->string('address', 200)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('office_tel', 20)->nullable();
            $table->string('contact_tel', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('national', 50)->nullable();
            $table->string('religion', 20)->nullable();
            $table->string('title', 20)->nullable();
            $table->string('enroll_sn', 20)->nullable();
            $table->string('ssn', 20)->nullable();
            $table->dateTime('update_time', 6)->nullable();
            $table->date('hire_date')->nullable();
            $table->integer('verify_mode')->nullable();
            $table->string('city', 20)->nullable();
            $table->smallInteger('emp_type')->nullable();
            $table->boolean('enable_payroll');
            $table->smallInteger('app_status')->nullable();
            $table->smallInteger('app_role')->nullable();
            $table->string('email', 50)->nullable();
            $table->dateTime('last_login', 6)->nullable();
            $table->boolean('is_active');
            $table->string('session_key', 32)->nullable();
            $table->string('login_ip', 32)->nullable();
            $table->integer('department_id')->nullable()->index();
            $table->integer('position_id')->nullable()->index();
            $table->integer('leave_group')->nullable();
            $table->bigInteger('emp_code_digit')->nullable();
            $table->integer('superior_id')->nullable()->index();
            $table->integer('company_id')->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('personnel_employee');
    }
}
