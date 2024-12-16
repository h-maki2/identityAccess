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
    private const verifiedUpdateBaseUrl = 'http://localhost:8080/verifiedUpdate';
        
    public static function create(
        UserEmail $toAddress,
        OneTimeToken $oneTimeToken,
        OneTimePassword $oneTimePassword
    ): SendEmailDto
    {
        $templateValiables = [
            'verifiedUpdateUrl' => self::verifiedUpdateBaseUrl . '?token=' . $oneTimeToken->tokenValue()->value,
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