<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('systems', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('card_id');
            $table->string('list_name', 150);
            $table->string('system_url', 500);
            $table->string('modal_icon', 100)->default('fa-link');
            $table->boolean('system_status')->default(true);
            $table->boolean('require_auto_login')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('date_created')->useCurrent();
            $table->timestamp('date_updated')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('card_id', 'fk_systems_card')
                  ->references('id')
                  ->on('cards')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('systems');
    }
};
