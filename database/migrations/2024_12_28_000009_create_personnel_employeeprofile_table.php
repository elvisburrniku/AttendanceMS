<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelEmployeeprofileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('personnel_employeeprofile')) {
            Schema::create('personnel_employeeprofile', function (Blueprint $table) {
                $table->id();
                $table->text('column_order')->default('');
                $table->text('preferences')->default('');
                $table->timestamp('pwd_update_time')->nullable();
                $table->integer('emp_id');
                $table->text('disabled_fields')->default('');
                $table->timestamps();
                
                $table->index('emp_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personnel_employeeprofile');
    }
}