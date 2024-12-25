<?php

namespace packages\adapter\view\changePassword\blade;

use packages\adapter\presenter\changePassword\blade\BladeChangePasswordPresenter;

class ChangePasswordView
{
    private BladeChangePasswordPresenter $presenter;

    public function __construct(BladeChangePasswordPresenter $presenter)
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
        return view('changePassword.changePasswordCompleted');
    }

    private function faildResponse()
    {
        return redirect()
                ->back()
                ->withErrors($this->presenter->faildResponseData())
                ->withInput();
    }
}