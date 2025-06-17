<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeChangesToAttendanceCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_comments', 'date')) {
                $table->date('date')->nullable()->after('text');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_comments', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
}
