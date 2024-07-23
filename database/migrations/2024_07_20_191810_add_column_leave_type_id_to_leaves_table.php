<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnLeaveTypeIdToLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('leaves')) {
            Schema::create('leaves', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('emp_id');
                $table->string('type');
                $table->string('comment');
                $table->dateTime('start_date');
                $table->dateTime('end_date');
                $table->double('total_days')->default(0);
                $table->timestamps();
            });
        }
        
        Schema::table('leaves', function (Blueprint $table) {
            $table->unsignedInteger('leave_type_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn('leave_type_id');
        });
    }
}
