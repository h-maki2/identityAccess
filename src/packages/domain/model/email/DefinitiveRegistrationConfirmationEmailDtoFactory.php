<?php

namespace packages\domain\model\email;

use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenExpiration;
use packages\domain\model\email\SendEmailDto;

/**
 * 本登録済み更新メールDTOのファクトリ
 */
class DefinitiveRegistrationConfirmationEmailDtoFactory
{
    private const DefinitiveRegisterUrl = 'http://localhost:8080/definitiveRegister';
        
    public static function create(
        UserEmail $toAddress,
        OneTimeToken $oneTimeToken,
        OneTimePassword $oneTimePassword
    ): SendEmailDto
    {
        $templateValiables = [
            'definitiveRegisterUrl' => self::DefinitiveRegisterUrl . '?token=' . $oneTimeToken->tokenValue()->value,
            'oneTimePassword' => $oneTimePassword->value,
            'expirationHours' => OneTimeTokenExpiration::expirationHours() . '時間後'
        ];
        return new SendEmailDto(
            'test@example.com',
            $toAddress->value,
            'システムテスト',
            '本登録確認メール',
            'email.definitiveRegistrationConfirmation.definitiveRegistrationConfirmationMail',
            $templateValiables
        );
    }
}