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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('type')->default('Operation');
            $table->boolean('disabled')->default(0);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('type')->default('setting');
            $table->boolean('disabled')->default(0);
            $table->timestamps();
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id');
            $table->foreignId('permission_id');

            $table->primary(['role_id', 'permission_id']);

            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onUpdate('cascade');
        });

        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('desc')->nullable();
            $table->string('type')->default('status');
            $table->integer('priority')->default(999);
            $table->timestamps();
        });

        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('bank');
            $table->string('acc_name');
            $table->string('acc_no');
            $table->string('image')->nullable();
            $table->integer('priority')->default(999);
            $table->boolean('disabled')->default(0);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('nickname')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->date('dob')->nullable();
            $table->string('social_link')->nullable();
            $table->string('other_social')->nullable();
            $table->foreignId('weekday_id')->nullable();
            $table->foreignId('gender_id')->nullable();
            $table->foreignId('role_id');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('provider_token')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->boolean('disabled')->default(0);
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('gender_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('weekday_id')->references('id')->on('statuses')->onDelete('cascade');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('dates', function (Blueprint $table) {
            $table->id();
            $table->integer('day')->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->string('type')->default('date');
            $table->timestamps();
        });

        Schema::create('dateables', function (Blueprint $table) {
            $table->foreignId('date_id');
            $table->foreignId('dateable_id');
            $table->string('dateable_type');

            $table->primary(['date_id', 'dateable_id', 'dateable_type']);

            $table->foreign('date_id')->references('id')->on('dates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('dates');
        Schema::dropIfExists('dateables');
        Schema::dropIfExists('banks');
    }
};
