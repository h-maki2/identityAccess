<?php

namespace packages\domain\model\email;

use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\email\SendEmailDto;

/**
 * 本登録済み更新メールDTOのファクトリ
 */
class DefinitiveRegistrationCompletedEmailDtoFactory
{
    private const DefinitiveRegistrationCompleteBaseUrl = 'http://localhost:8080/definitiveRegistrationComplete';
        
    public static function create(
        UserEmail $toAddress,
        OneTimeToken $oneTimeToken,
        OneTimePassword $oneTimePassword
    ): SendEmailDto
    {
        $templateValiables = [
            'DefinitiveRegistrationCompletedUpdateUrl' => self::DefinitiveRegistrationCompleteBaseUrl . '?token=' . $oneTimeToken->tokenValue()->value,
            'oneTimePassword' => $oneTimePassword->value
        ];
        return new SendEmailDto(
            'test@example.com',
            $toAddress->value,
            'システムテスト',
            '本登録済みメール',
            'email.DefinitiveRegistrationCompletedUpdate.DefinitiveRegistrationCompletedUpdateMail',
            $templateValiables
        );
    }
}