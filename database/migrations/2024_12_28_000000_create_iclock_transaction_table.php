
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIclockTransactionTable extends Migration
{
    public function up()
    {
        Schema::create('iclock_transaction', function (Blueprint $table) {
            $table->id();
            $table->string('emp_code', 20)->index();
            $table->dateTime('punch_time', 6)->index();
            $table->string('punch_state', 5);
            $table->integer('verify_type');
            $table->string('work_code', 20)->nullable();
            $table->string('terminal_sn', 50)->nullable();
            $table->string('terminal_alias', 50)->nullable();
            $table->string('area_alias', 100)->nullable();
            $table->double('longitude')->nullable();
            $table->double('latitude')->nullable();
            $table->longText('gps_location')->nullable();
            $table->string('mobile', 50)->nullable();
            $table->smallInteger('source')->nullable();
            $table->smallInteger('purpose')->nullable();
            $table->string('crc', 100)->nullable();
            $table->smallInteger('is_attendance')->nullable();
            $table->string('reserved', 100)->nullable();
            $table->dateTime('upload_time', 6)->nullable();
            $table->smallInteger('sync_status')->nullable();
            $table->dateTime('sync_time', 6)->nullable();
            $table->smallInteger('is_mask')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('emp_id')->nullable()->index();
            $table->integer('terminal_id')->nullable()->index();
            $table->string('company_code', 50)->nullable()->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('iclock_transaction');
    }
}
