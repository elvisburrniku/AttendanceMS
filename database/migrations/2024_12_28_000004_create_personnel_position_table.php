
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelPositionTable extends Migration
{
    public function up()
    {
        Schema::create('personnel_position', function (Blueprint $table) {
            $table->id();
            $table->string('position_code', 50)->index();
            $table->string('position_name', 200);
            $table->boolean('is_default')->default(false);
            $table->integer('parent_position_id')->nullable()->index();
            $table->integer('company_id')->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('personnel_position');
    }
}
