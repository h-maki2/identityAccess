<?php

namespace packages\adapter\view\authentication\verifiedUpdate\blade;

use packages\adapter\presenter\authentication\verifiedUpdate\blade\BladeVerifiedUpdatePresenter;

class BladeVerifiedUpdateView
{
    private BladeVerifiedUpdatePresenter $presenter;

    public function __construct(BladeVerifiedUpdatePresenter $presenter)
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
        return view('authentication.verifiedUpdate.verifiedUpdateComplete');
    }

    public function faildResponse()
    {
        return redirect()
                ->back()
                ->withErrors($this->presenter->responseDate())
                ->withInput();
    }
}