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
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('discount_percent')->default(0)->after('th_price');
            $table->double('final_price')->default(0.0)->after('discount_percent');
            $table->double('th_final_price')->default(0.0)->after('final_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
            $table->dropColumn('final_price');
            $table->dropColumn('th_final_price');
        });
    }
};
