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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name')->unique();
            $table->double('price')->unsigned()->default(0.0);
            $table->double('th_price')->unsigned()->default(0.0);
            $table->text('desc')->nullable();
            $table->text('image')->nullable();
            $table->boolean('disabled')->default(0);
            $table->foreignId('astrologer_id');
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('th_currency_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('astrologer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('th_currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });

        Schema::create('remarks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('is_image')->default(false);
            $table->timestamps();
        });

        Schema::create('remarkables', function (Blueprint $table) {
            $table->foreignId('remark_id');
            $table->foreignId('remarkable_id');
            $table->string('remarkable_type');

            $table->primary(['remark_id', 'remarkable_id', 'remarkable_type'], 'remark_id_remarkable_id_type');

            $table->foreign('remark_id')->references('id')->on('remarks')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->nullable();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('desc')->nullable();
            $table->double('price')->unsigned()->default(0.0);
            $table->double('th_price')->unsigned()->default(0.0);
            $table->text('image')->nullable();
            $table->boolean('disabled')->default(0);
            $table->integer('priority')->default(0);
            $table->foreignId('type_id')->nullable();
            $table->foreignId('currency_id')->nullable();
            $table->foreignId('th_currency_id')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('th_currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
        Schema::dropIfExists('package_remarks');
        Schema::dropIfExists('items');
    }
};
