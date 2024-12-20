<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録</title>
</head>
<body>
    <h1>ユーザー登録</h1>
    <form action="/provisionalRegister" method="post">
        @csrf
        <div><input type="text" name="email" placeholder="メールアドレス" value="{{ old('email', '') }}"></div>
        @if ($errors->has('email'))
            @foreach ($errors->get('email') as $errorMessage)
                <p style="color: red;">{{ $errorMessage }}</p>
            @endforeach
        @endif
        <div><input type="password" name="password" placeholder="パスワード" value="{{ old('password', '') }}"></div>
        @if ($errors->has('password'))
            @foreach ($errors->get('password') as $errorMessage)
                <p style="color: red;">{{ $errorMessage }}</p>
            @endforeach
        @endif
        <div><input type="password" name="passwordConfirmation" placeholder="パスワード確認" value="{{ old('passwordConfirmation', '') }}"></div>
        @if ($errors->has('passwordConfirmation'))
            @foreach ($errors->get('passwordConfirmation') as $errorMessage)
                <p style="color: red;">{{ $errorMessage }}</p>
            @endforeach
        @endif
        <div><input type="submit" value="送信"></div>
    </form>
</body>
</html>