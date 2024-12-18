<?php

namespace packages\domain\model\email;

use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\email\SendEmailDto;

/**
 * 本登録済み更新メールDTOのファクトリ
 */
class DefinitiveRegistrationConfirmedUpdateEmailDtoFactory
{
    private const DefinitiveRegistrationConfirmedUpdateBaseUrl = 'http://localhost:8080/DefinitiveRegistrationConfirmedUpdate';
        
    public static function create(
        UserEmail $toAddress,
        OneTimeToken $oneTimeToken,
        OneTimePassword $oneTimePassword
    ): SendEmailDto
    {
        $templateValiables = [
            'DefinitiveRegistrationConfirmedUpdateUrl' => self::DefinitiveRegistrationConfirmedUpdateBaseUrl . '?token=' . $oneTimeToken->tokenValue()->value,
            'oneTimePassword' => $oneTimePassword->value
        ];
        return new SendEmailDto(
            'test@example.com',
            $toAddress->value,
            'システムテスト',
            '本登録済みメール',
            'email.DefinitiveRegistrationConfirmedUpdate.DefinitiveRegistrationConfirmedUpdateMail',
            $templateValiables
        );
    }
}