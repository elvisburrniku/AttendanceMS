
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttAttemployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('att_attemployee', function (Blueprint $table) {
            $table->id();
            $table->timestamp('create_time')->nullable();
            $table->timestamp('change_time')->nullable();
            $table->integer('status')->default(0);
            $table->boolean('enable_attendance')->default(true);
            $table->boolean('enable_schedule')->default(true);
            $table->boolean('enable_overtime')->default(true);
            $table->boolean('enable_holiday')->default(true);
            $table->boolean('enable_compensatory')->default(false);
            $table->unsignedBigInteger('emp_id');
            
            $table->foreign('emp_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('att_attemployee');
    }
}
