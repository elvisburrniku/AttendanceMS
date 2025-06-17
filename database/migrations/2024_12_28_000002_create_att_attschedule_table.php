
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttAttscheduleTable extends Migration
{
    public function up()
    {
        Schema::create('att_attschedule', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('employee_id')->index();
            $table->integer('shift_id')->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('att_attschedule');
    }
}
