
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttTimeintervalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('att_timeinterval', function (Blueprint $table) {
            $table->id();
            $table->string('alias', 50)->index();
            $table->smallInteger('use_mode');
            $table->time('in_time');
            $table->integer('in_ahead_margin');
            $table->integer('in_above_margin');
            $table->integer('out_ahead_margin');
            $table->integer('out_above_margin');
            $table->integer('duration');
            $table->smallInteger('in_required');
            $table->smallInteger('out_required');
            $table->integer('allow_late');
            $table->integer('allow_leave_early');
            $table->double('work_day');
            $table->smallInteger('early_in');
            $table->integer('min_early_in');
            $table->smallInteger('late_out');
            $table->integer('min_late_out');
            $table->smallInteger('overtime_lv');
            $table->smallInteger('overtime_lv1');
            $table->smallInteger('overtime_lv2');
            $table->smallInteger('overtime_lv3');
            $table->smallInteger('multiple_punch');
            $table->smallInteger('available_interval_type');
            $table->integer('available_interval');
            $table->integer('work_time_duration');
            $table->smallInteger('func_key');
            $table->smallInteger('work_type');
            $table->time('day_change');
            $table->boolean('enable_early_in');
            $table->boolean('enable_late_out');
            $table->boolean('enable_overtime');
            $table->char('ot_rule', 32)->nullable();
            $table->string('color_setting', 30)->nullable();
            $table->boolean('enable_max_ot_limit');
            $table->integer('max_ot_limit');
            $table->boolean('count_early_in_interval');
            $table->boolean('count_late_out_interval');
            $table->integer('ot_pay_code_id')->nullable()->index();
            $table->smallInteger('overtime_policy');
            $table->integer('company_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('att_timeinterval');
    }
}
