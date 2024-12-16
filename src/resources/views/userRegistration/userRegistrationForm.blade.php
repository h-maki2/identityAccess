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
        <div><input type="text" name="email" placeholder="メールアドレス" value="{{isset($viewModel->email) ? $viewModel->email : ''}}"></div>
        @if (isset($viewModel->validationErrorList['email']))
            @foreach ($viewModel->validationErrorList['email'] as $validationErrorMessage)
                <div style="color: red;">
                    {{ $validationErrorMessage }}
                </div>
            @endforeach
        @endif
        <div><input type="password" name="password" placeholder="パスワード" value="{{isset($viewModel->password) ? $viewModel->password : ''}}"></div>
        @if (isset($viewModel->validationErrorList['password']))
            @foreach ($viewModel->validationErrorList['password'] as $validationErrorMessage)
                <div style="color: red;">
                    {{ $validationErrorMessage }}
                </div>
            @endforeach
        @endif
        <div><input type="password" name="passwordConfirmation" placeholder="パスワード確認" value="{{isset($viewModel->passwordConfirmation) ? $viewModel->passwordConfirmation : ''}}"></div>
        @if (isset($viewModel->validationErrorList['passwordConfirmation']))
            @foreach ($viewModel->validationErrorList['passwordConfirmation'] as $validationErrorMessage)
                <div style="color: red;">
                    {{ $validationErrorMessage }}
                </div>
            @endforeach
        @endif
        <div><input type="submit" value="送信"></div>
    </form>
</body>
</html>