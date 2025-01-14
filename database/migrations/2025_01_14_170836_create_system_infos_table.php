<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_infos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('email')->nullable();
            $table->text('facebook_link')->nullable();
            $table->text('tiktok_link')->nullable();
            $table->text('viber_link')->nullable();
            $table->text('telegram_link')->nullable();
            $table->text('instagram_link')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->integer('priority')->default(0);
            $table->timestamps();
        });

        Schema::create('system_phones', function (Blueprint $table) {
            $table->foreignId('system_id');
            $table->foreignId('phone_id');

            $table->primary(['system_id', 'phone_id']);

            $table->foreign('system_id')->references('id')->on('system_infos')->onDelete('cascade');
            $table->foreign('phone_id')->references('id')->on('phones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_infos');
        Schema::dropIfExists('phones');
        Schema::dropIfExists('system_phones');
    }
};
