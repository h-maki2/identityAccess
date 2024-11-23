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
        Schema::create('authentication_information', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->string('email', 255);
            $table->string('password', 255);
            $table->boolean('verification_status');
            $table->integer('failed_login_count')->default(0);
            $table->boolean('login_restriction_status');
            $table->dateTime('next_login_allowed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authentication_information');
    }
};
