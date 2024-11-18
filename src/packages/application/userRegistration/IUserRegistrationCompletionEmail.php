<?php

namespace packages\application\userRegistration;

use packages\application\common\email\SendEmailDto;

interface IUserRegistrationCompletionEmail
{
    public function send(SendEmailDto $sendEmailDto): void;
}