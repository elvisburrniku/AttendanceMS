<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttShiftdetailTable extends Migration
{
    public function up()
    {
        Schema::create('att_shiftdetail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id')->index();
            $table->unsignedBigInteger('time_interval_id')->index();
            $table->smallInteger('work_type')->default(0);
            $table->smallInteger('day_of_week')->default(0);
            $table->timestamps();
            
            $table->foreign('shift_id')->references('id')->on('att_attshift')->onDelete('cascade');
            $table->foreign('time_interval_id')->references('id')->on('att_timeinterval')->onDelete('cascade');
            $table->unique(['shift_id', 'time_interval_id', 'day_of_week']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('att_shiftdetail');
    }
}