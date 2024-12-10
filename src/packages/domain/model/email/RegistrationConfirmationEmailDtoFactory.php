<?php

namespace packages\domain\model\email;

use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\email\SendEmailDto;

/**
 * 認証済み更新メールDTOのファクトリ
 */
class VerifiedUpdateEmailDtoFactory
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
            '認証済みメール',
            'email.verifiedUpdate.verifiedUpdateMail',
            $templateValiables
        );
    }
}