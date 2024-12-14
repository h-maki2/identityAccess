<?php

namespace packages\domain\model\email;

use packages\domain\model\email\SendEmailDto;

interface IEmailSender
{
    public function send(SendEmailDto $sendEmailDto): void;
}