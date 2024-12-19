<!DOCTYPE html>
<html>
<head>
    <title>本登録確認メール</title>
</head>
<body>
    このたびは会員登録いただき、誠にありがとうございます。<br>
    以下のURLにアクセスして、本登録を完了してください。<br><br>

    本登録確認URL:<br>
    <p><a href="{{$definitiveRegisterUrl}}">{{ $definitiveRegisterUrl }}</a><p>

    上記のURLにアクセスすると、ワンタイムパスワードの入力画面が表示されます。<br>
    以下のワンタイムパスワードを入力してください。<br><br>

    ワンタイムパスワード: {{ $oneTimePassword }}<br><br>

    ※ワンタイムパスワードの有効期限は{{ $expirationHours }}までとなります。期限を過ぎると無効となりますので、ご注意ください。
</body>
</html>