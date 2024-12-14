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
        Schema::create('auth_confirmations', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->char('one_time_token_value', 26);
            $table->dateTime('one_time_token_expiration');
            $table->char('one_time_password', 6);
            $table->timestamps(); // created_at, updated_at カラムを自動追加

            // 外部キー制約
            $table->foreign('user_id')->references('user_id')->on('authentication_informations')
                ->onDelete('cascade'); // 親レコード削除時に自動削除
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_confirmations');
    }
};
