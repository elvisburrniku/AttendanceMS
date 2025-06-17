
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelEmployeeAreaTable extends Migration
{
    public function up()
    {
        Schema::create('personnel_employee_area', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->index();
            $table->integer('area_id')->index();
            
            $table->foreign('employee_id')->references('id')->on('personnel_employee')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('personnel_area')->onDelete('cascade');
            
            $table->unique(['employee_id', 'area_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('personnel_employee_area');
    }
}
