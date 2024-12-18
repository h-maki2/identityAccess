<?php

namespace packages\adapter\view\authentication\definitiveRegistrationCompleted\blade;

use packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade\BladeDefinitiveRegistrationCompletedUpdatePresenter;

class BladeDefinitiveRegistrationCompletedUpdateView
{
    private BladeDefinitiveRegistrationCompletedUpdatePresenter $presenter;

    public function __construct(BladeDefinitiveRegistrationCompletedUpdatePresenter $presenter)
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
        return view('authentication.DefinitiveRegistrationCompletedUpdate.DefinitiveRegistrationCompletedUpdateComplete');
    }

    public function faildResponse()
    {
        return redirect()
                ->back()
                ->withErrors($this->presenter->responseDate())
                ->withInput();
    }
}