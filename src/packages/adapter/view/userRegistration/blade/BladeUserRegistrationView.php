<?php

namespace packages\adapter\view\userRegistration\blade;

use Illuminate\Validation\Validator;
use packages\adapter\presenter\userRegistration\blade\BladeUserRegistrationPresenter;
use packages\adapter\presenter\userRegistration\UserRegistrationView;

class BladeUserRegistrationView
{
    private BladeUserRegistrationPresenter $presenter;

    public function __construct(BladeUserRegistrationPresenter $presenter)
    {
        $this->presenter = $presenter;
    }

    public function response(): mixed
    {
        if ($this->presenter->isSuccess()) {
            return $this->successResponse();
        }

        return $this->faildResponse();
    }

    private function successResponse()
    {
        return view('userRegistration.userRegistrationComplete');
    }

    private function faildResponse()
    {
        return redirect()
                ->back()
                ->withErrors($this->getValidationError())
                ->withInput();
    }

    private function getValidationError(): Validator
    {
        return $this->presenter->validationError;
    }
}