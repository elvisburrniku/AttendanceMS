<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('leaves')) {
            $this->down();
        }
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaves');
    }
}
