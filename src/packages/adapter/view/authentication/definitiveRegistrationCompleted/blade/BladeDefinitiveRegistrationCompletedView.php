<?php

namespace packages\adapter\view\authentication\UserDefinitiveRegistration\blade;

use packages\adapter\presenter\authentication\UserDefinitiveRegistration\blade\BladeUserDefinitiveRegistrationPresenter;

class BladeUserDefinitiveRegistrationView
{
    private BladeUserDefinitiveRegistrationPresenter $presenter;

    public function __construct(BladeUserDefinitiveRegistrationPresenter $presenter)
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
        return view('authentication.UserDefinitiveRegistration.UserDefinitiveRegistration');
    }

    public function faildResponse()
    {
        return redirect()
                ->back()
                ->withErrors($this->presenter->responseDate())
                ->withInput();
    }
}