<?php

namespace packages\adapter\view\authentication\definitiveRegistrationCompleted\blade;

use packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade\BladeDefinitiveRegistrationConfirmedUpdatePresenter;

class BladeDefinitiveRegistrationConfirmedUpdateView
{
    private BladeDefinitiveRegistrationConfirmedUpdatePresenter $presenter;

    public function __construct(BladeDefinitiveRegistrationConfirmedUpdatePresenter $presenter)
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
        return view('authentication.DefinitiveRegistrationConfirmedUpdate.DefinitiveRegistrationConfirmedUpdateComplete');
    }

    public function faildResponse()
    {
        return redirect()
                ->back()
                ->withErrors($this->presenter->responseDate())
                ->withInput();
    }
}