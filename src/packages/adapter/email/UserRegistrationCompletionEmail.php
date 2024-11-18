<?php

namespace packages\adapter\email;

use packages\application\userRegistration\IUserRegistrationCompletionEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailHandler;
use packages\application\common\email\SendEmailDto;

class UserRegistrationCompletionEmail implements IUserRegistrationCompletionEmail
{
    public function send(SendEmailDto $sendEmailDto): void
    {
        Mail::to($sendEmailDto->toAddress)->send(new EmailHandler($sendEmailDto));
    }
}