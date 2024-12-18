<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\authenticationAccount;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // DB::table('oauth_clients')
        // ->where('id', 4) // 対象のクライアントIDに変更
        // ->update(['redirect' => 'http://identity.todoapp.local/test/token']);

        // サンプルデータの作成
        AuthenticationAccount::create([
            'user_id' => (string) '11112',
            'username' => 'example_user',
            'password' => bcrypt('password123'), // パスワードをハッシュ化
            'verification_status' => true,
            'failed_login_attempts' => 0,
            'next_login_at' => null,
        ]);
    }
}
