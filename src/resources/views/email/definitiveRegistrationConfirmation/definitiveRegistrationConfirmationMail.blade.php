<!DOCTYPE html>
<html>
<head>
    <title>本登録確認メール</title>
</head>
<body>
    <p>下記のURLから本登録済みの更新を行い、本登録の完了をお願いします。</p>
    <p><a href="{{$definitiveRegistrationConfirmationUpdateUrl}}">{{ $definitiveRegistrationConfirmationUpdateUrl }}</a><p>
    <p>ワンタイムパスワード: {{ $oneTimePassword }}</p>
</body>
</html>