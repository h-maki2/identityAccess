<?php

namespace packages\domain\service\userRegistration;

use packages\domain\model\common\email\SendEmailDto;

interface IUserRegistrationCompletionEmail
{
    public function send(SendEmailDto $sendEmailDto): void;
}