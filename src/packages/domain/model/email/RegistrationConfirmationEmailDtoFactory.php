<?php

namespace packages\domain\model\email;

use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\common\email\SendEmailDto;

/**
 * 本登録確認メールDTOのファクトリ
 */
class RegistrationConfirmationEmailDtoFactory
{
    public static function create(
        UserEmail $toAddress,
        OneTimeToken $oneTimeToken,
        OneTimePassword $oneTimePassword
    ): SendEmailDto
    {
        $templateValiables = [
            'oneTimeToken' => $oneTimeToken->value(),
            'oneTimePassword' => $oneTimePassword->value
        ];
        return new SendEmailDto(
            'test@example.com',
            $toAddress->value,
            'システムテスト',
            '本登録確認メール',
            'email.test',
            $templateValiables
        );
    }
}