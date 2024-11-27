<?php

namespace packages\adapter\email;

use Illuminate\Support\Facades\Mail;
use App\Mail\EmailHandler;
use packages\domain\model\email\SendEmailDto;
use packages\domain\model\email\IEmailSender;

class LaravelEmailSender implements IEmailSender
{
    public function send(SendEmailDto $sendEmailDto): void
    {
        Mail::to($sendEmailDto->toAddress)->send(new EmailHandler($sendEmailDto));
    }
}