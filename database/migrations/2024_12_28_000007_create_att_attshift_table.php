
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttAttshiftTable extends Migration
{
    public function up()
    {
        Schema::create('att_attshift', function (Blueprint $table) {
            $table->id();
            $table->string('alias', 50);
            $table->smallInteger('cycle_unit');
            $table->integer('shift_cycle');
            $table->boolean('work_weekend');
            $table->smallInteger('weekend_type');
            $table->boolean('work_day_off');
            $table->smallInteger('day_off_type');
            $table->smallInteger('auto_shift');
            $table->boolean('enable_ot_rule');
            $table->smallInteger('frequency');
            $table->char('ot_rule', 32)->nullable();
            $table->integer('company_id')->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('att_attshift');
    }
}
