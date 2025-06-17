
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelDepartmentTable extends Migration
{
    public function up()
    {
        Schema::create('personnel_department', function (Blueprint $table) {
            $table->id();
            $table->string('dept_code', 50)->index();
            $table->string('dept_name', 200);
            $table->boolean('is_default')->default(false);
            $table->integer('parent_dept_id')->nullable()->index();
            $table->integer('dept_manager_id')->nullable()->index();
            $table->integer('company_id')->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('personnel_department');
    }
}
