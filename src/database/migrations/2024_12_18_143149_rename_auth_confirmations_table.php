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
        Schema::rename('auth_confirmations', 'definitive_registration_confirmations'); // テーブル名の変更
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('definitive_registration_confirmations', 'auth_confirmations'); // テーブル名の変更
    }
};
