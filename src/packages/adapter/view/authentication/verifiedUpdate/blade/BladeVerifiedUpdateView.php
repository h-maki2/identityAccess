<?php

namespace packages\adapter\view\authentication\definitiveRegistrationCompleted\blade;

use packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade\BladeDefinitiveRegistrationCompletedPresenter;

class BladeDefinitiveRegistrationCompletedView
{
    private BladeDefinitiveRegistrationCompletedPresenter $presenter;

    public function __construct(BladeDefinitiveRegistrationCompletedPresenter $presenter)
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
        return view('authentication.DefinitiveRegistrationCompleted.DefinitiveRegistrationCompletedComplete');
    }

    public function faildResponse()
    {
        return redirect()
                ->back()
                ->withErrors($this->presenter->responseDate())
                ->withInput();
    }
}