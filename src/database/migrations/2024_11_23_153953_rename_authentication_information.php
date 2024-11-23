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
        // テーブル名を変更
        Schema::rename('authentication_information', 'authentication_informations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // テーブル名を変更
        Schema::rename('authentication_informations', 'authentication_information');
    }
};
