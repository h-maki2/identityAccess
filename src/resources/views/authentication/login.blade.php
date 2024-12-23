<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
</head>
<body>
    <h1>ログイン</h1>
    <form action="/definitiveRegister" method="post">
        @csrf
        @if ($errors->has('loginFaild'))
            <p style="color: red;">メールアドレスかパスワードが異なります。</p>
        @endif
        @if ($errors->has('accountLocked') && $errors->first('accountLocked'))
            <p style="color: red;">アカウントがロックされています。<br>少し時間を空けてお試しください。</p>
        @endif
        <div><input type="text" name="email" placeholder="メールアドレス" value="{{ old('email', '') }}"></div>
        <input type="password" name="password" placeholder="パスワード" value="{{ old('password', '') }}">
        <div><input type="submit" value="ログイン"></div>
    </form>
</body>
</html>