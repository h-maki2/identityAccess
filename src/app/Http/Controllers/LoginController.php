<?php

namespace App\Http\Controllers;

use Laravel\Passport\Client;
use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        // 認証が成功した場合、セッションにユーザーを保存
        $a = UserProfile::where('username', 'example_user')->first();
        $b = UserProfile::where('user_id', '11111')->first();
        // print_r($b->user_id);
        Auth::guard('web')->loginUsingId($b->user_id);
        // print Auth::id();
        // if (Auth::check()) {
        //     print 'aaaaa';
        // }
        // return;

        $client = Client::where('password_client', 1)->where('id', '4')->first();
        // return response()->json([
        //     'authorization_url' => url('/oauth/authorize?response_type=code&client_id='.$client->id.'&redirect_uri='.$request->redirect_uri)
        // ]);
        return response()->json([
            'authorization_url' => url('/oauth/authorize?response_type=code&client_id='.$client->id.'&redirect_uri=http://identity.todoapp.local/test/token')
        ]);
    }

    public function token(Request $request)
    {
        var_dump($request['code']);
    }
}