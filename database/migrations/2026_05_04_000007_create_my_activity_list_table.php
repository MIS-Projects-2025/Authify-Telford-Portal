<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('my_activity_list', function (Blueprint $table) {
            $table->id();
            $table->string('emp_id', 45)->nullable();
            $table->string('emp_name', 45)->nullable();
            $table->string('shift', 45)->nullable();
            $table->string('my_activity', 145)->nullable();
            $table->string('machine', 45)->nullable();
            $table->string('opt_id', 45)->nullable();
            $table->string('opt_name', 45)->nullable();
            $table->longText('opt_remarks')->nullable();
            $table->string('log_time', 45)->nullable();
            $table->string('time_out', 45)->nullable();
            $table->longText('note')->nullable();
            $table->string('status', 45)->nullable();
            $table->string('approver_id', 45)->nullable();
            $table->string('approver_name', 45)->nullable();
            $table->string('approve_date', 45)->nullable();
            $table->longText('remarks')->nullable();
            $table->string('rejector_id', 45)->nullable();
            $table->string('rejector_name', 45)->nullable();
            $table->longText('reject_remarks')->nullable();
            $table->string('rejected_date', 45)->nullable();
            $table->string('item_status', 45)->nullable();
            $table->string('deleted_by', 45)->nullable();
            $table->string('deleted_date', 45)->nullable();
            $table->string('restored_by', 45)->nullable();
            $table->string('restored_date', 45)->nullable();
            $table->dateTime('date_created')->useCurrent();
            $table->dateTime('date_updated')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('my_activity_list');
    }
};
