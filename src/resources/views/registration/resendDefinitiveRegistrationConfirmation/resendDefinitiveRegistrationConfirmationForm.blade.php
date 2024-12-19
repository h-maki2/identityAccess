<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>本登録確認メール再送</title>
</head>
<body>
    <h1>本登録確認メール再送</h1>
    <p>登録したメールアドレスを入力してください。</p>
    <form action="/resend" method="post">
        @csrf
        <div><input type="text" name="メールアドレス" placeholder="ワンタイムパスワード" value="{{ old('oneTimePassword', '') }}"></div>
        @if ($errors->has('validationErrorMessage'))
            <p style="color: red;">{{ $errors->first('validationErrorMessage') }}</p>
        @endif
        <div><input type="submit" value="本登録確認メール再送"></div>
    </form>
</body>
</html>