<?php

namespace packages\domain\model\email;

use packages\domain\model\common\email\SendEmailDto;

interface IEmailSender
{
    public function send(SendEmailDto $sendEmailDto): void;
}