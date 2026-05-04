<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authify_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('token', 45)->unique();
            $table->string('emp_id', 45);
            $table->string('emp_pass', 45)->nullable();
            $table->string('emp_name', 255)->nullable();
            $table->string('emp_firstname', 255)->nullable();
            $table->string('emp_jobtitle', 255)->nullable();
            $table->string('emp_position', 255)->nullable();
            $table->string('emp_dept', 255)->nullable();
            $table->string('emp_prodline', 255)->nullable();
            $table->string('emp_station', 255)->nullable();
            $table->string('emp_from', 50)->nullable();
            $table->dateTime('generated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authify_sessions');
    }
};
