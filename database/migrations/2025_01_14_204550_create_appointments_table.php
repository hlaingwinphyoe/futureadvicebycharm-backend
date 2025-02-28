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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->integer('appointment_no');
            $table->string('appointment_month');
            $table->double('total_price', 20, 2)->default(0.0);
            $table->double('th_total_price', 20, 2)->default(0.0);
            $table->date('appointment_date');
            $table->longText('desc')->nullable();
            $table->foreignId('refer_id')->nullable();
            $table->double('balance')->default(0.0);
            $table->double('th_balance')->default(0.0);
            $table->double('discount_amt')->default(0.0);
            $table->text('transaction_image')->nullable();
            $table->string('transaction_no')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->foreignId('discountype_id')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->constrained()->cascadeOnDelete();
            $table->foreignId('paymentype_id')->nullable();
            $table->timestamps();
        });

        Schema::create('appointment_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->double('price', 20, 2);
            $table->double('th_price', 20, 2);
            $table->double('balance')->default(0.0);
            $table->double('th_balance')->default(0.0);
            $table->double('discount_amt')->default(0.0);
            $table->foreignId('discountype_id')->nullable();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('th_currency_id')->nullable();
            $table->foreignId('status_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('appointment_packages');
    }
};
