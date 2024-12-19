<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>本登録確認</title>
</head>
<body>
    <h1>本登録確認</h1>
    <form action="/definitiveRegister" method="post">
        @csrf
        <div><input type="text" name="oneTimePassword" placeholder="ワンタイムパスワード" value="{{ old('oneTimePassword', '') }}"></div>
        @if ($errors->has('validationErrorMessage'))
            <p style="color: red;">{{ $errors->first('validationErrorMessage') }}</p>
        @endif
        <input type="hidden" name="oneTimeToken" value="{{ old('oneTimeToken', $oneTimeToken) }}">
        <div><input type="submit" value="送信"></div>
    </form>
</body>
</html>