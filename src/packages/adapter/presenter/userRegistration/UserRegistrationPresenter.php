<?php

namespace packages\adapter\presenter\userRegistration;

use packages\application\userRegistration\UserRegistrationResult;
use UserRegistrationView;

abstract class UserRegistrationPresenter
{
    protected UserRegistrationResult $result;
    protected UserRegistrationView $view;

    public function __construct(UserRegistrationView $view)
    {
        $this->view = $view;
    }

    abstract public function responseView(): mixed;

    public function setResult(UserRegistrationResult $result): void
    {
        $this->result = $result;
    }
}