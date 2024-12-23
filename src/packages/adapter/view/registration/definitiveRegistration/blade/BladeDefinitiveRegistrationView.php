<?php

namespace packages\adapter\view\registration\definitiveRegistration\blade;

use packages\adapter\presenter\registration\definitiveRegistration\blade\BladeDefinitiveRegistrationPresenter;

class BladeDefinitiveRegistrationView
{
    private BladeDefinitiveRegistrationPresenter $presenter;

    public function __construct(BladeDefinitiveRegistrationPresenter $presenter)
    {
        $this->presenter = $presenter;
    }

    public function response()
    {
        if ($this->presenter->isValidationError()) {
            return $this->faildResponse();
        }

        return $this->successResponse();
    }

    public function successResponse()
    {
        return view('registration.definitiveRegistration.definitiveRegistrationCompleted');
    }

    public function faildResponse()
    {
        return redirect('/definitiveRegister')
                ->withErrors($this->presenter->responseData())
                ->withInput();
    }
}