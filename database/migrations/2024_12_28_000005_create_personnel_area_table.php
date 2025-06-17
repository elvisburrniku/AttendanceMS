
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelAreaTable extends Migration
{
    public function up()
    {
        Schema::create('personnel_area', function (Blueprint $table) {
            $table->id();
            $table->string('area_code', 50)->index();
            $table->string('area_name', 200);
            $table->boolean('is_default')->default(false);
            $table->integer('parent_area_id')->nullable()->index();
            $table->integer('company_id')->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('personnel_area');
    }
}
