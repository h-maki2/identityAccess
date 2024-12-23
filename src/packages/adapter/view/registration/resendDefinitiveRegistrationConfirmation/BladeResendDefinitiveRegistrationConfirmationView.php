<?php

namespace packages\adapter\view\registration\resendDefinitiveRegistrationConfirmation;

use packages\adapter\presenter\registration\resendDefinitiveRegistrationConfirmation\blade\BladeResendDefinitiveRegistrationConfirmationPresenter;

class BladeResendDefinitiveRegistrationConfirmationView
{
    private BladeResendDefinitiveRegistrationConfirmationPresenter $presenter;

    public function __construct(BladeResendDefinitiveRegistrationConfirmationPresenter $presenter)
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

    private function successResponse()
    {
        return view('registration.resendDefinitiveRegistrationConfirmation.resendDefinitiveRegistrationConfirmationCompleted');
    }

    private function faildResponse()
    {
        return redirect('/resend')
                ->withErrors($this->presenter->responseData())
                ->withInput();
    }
}