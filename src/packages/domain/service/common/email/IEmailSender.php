<?php

namespace packages\service\common\email;

use packages\domain\model\common\email\SendEmailDto;

interface IEmailSender
{
    public function send(SendEmailDto $sendEmailDto): void;
}