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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->uuid('user_profile_id')->primary();
            $table->uuid('user_id');
            $table->string('user_name', 50);
            $table->string('self_introduction_text', 500);
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('authentication_informations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
