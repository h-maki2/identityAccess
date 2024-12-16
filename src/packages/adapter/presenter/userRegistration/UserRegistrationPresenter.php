<?php

namespace packages\adapter\presenter\userRegistration;

use packages\application\userRegistration\UserRegistrationResult;

abstract class UserRegistrationPresenter
{
    protected UserRegistrationResult $result;
    protected UserRegistrationView $view;

    public function __construct(UserRegistrationView $view)
    {
        $this->view = $view;
    }

    public function responseView(): mixed
    {
        $this->setHttpStatusToView();
        $this->setResponseDataToView();
        return $this->view->response();
    }

    public function setResult(UserRegistrationResult $result): void
    {
        $this->result = $result;
    }

    abstract protected function setResponseDataToView(): void;

    abstract protected function setHttpStatusToView(): void;
}