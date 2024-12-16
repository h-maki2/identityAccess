<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録</title>
</head>
<body>
    <h1>ユーザー登録</h1>
    <form action="/userRegistration" method="post">
        @csrf
        <div><input type="text" name="email" placeholder="メールアドレス"></div>
        <div><input type="password" name="password" placeholder="パスワード"></div>
        <div><input type="password" name="passwordConfirmation" placeholder="パスワード確認"></div>
        <div><input type="submit" value="送信"></div>
    </form>
</body>
</html>