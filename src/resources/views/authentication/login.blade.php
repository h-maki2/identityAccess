<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
</head>
<body>
    <h1>ログイン</h1>
    <form action="/login" method="post">
        @csrf
        @if ($errors->has('loginFaild'))
            <p style="color: red;">メールアドレスかパスワードが異なります。</p>
        @endif
        @if ($errors->has('accountLocked') && $errors->first('accountLocked'))
            <p style="color: red;">アカウントがロックされています。<br>少し時間を空けてお試しください。</p>
        @endif
        <div><input type="text" name="email" placeholder="メールアドレス" value="{{ old('email', '') }}"></div>
        <div><input type="password" name="password" placeholder="パスワード" value="{{ old('password', '') }}"></div>
        <input type="hidden" name="client_id" value="{{ old('client_id', $clientId) }}">
        <input type="hidden" name="redirect_url" value="{{ old('redirect_url', $redirectUrl) }}">
        <input type="hidden" name="response_type" value="{{ old('response_type', $responseType) }}">
        <input type="hidden" name="state" value="{{ old('state', $state) }}">
        <input type="hidden" name="scope" value="{{ old('scope', $scopes) }}">
        <div><input type="submit" value="ログイン"></div>
    </form>
</body>
</html>