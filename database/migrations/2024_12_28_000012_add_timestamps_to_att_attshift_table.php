<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToAttAttshiftTable extends Migration
{
    public function up()
    {
        Schema::table('att_attshift', function (Blueprint $table) {
            if (!Schema::hasColumn('att_attshift', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        Schema::table('att_attshift', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
}