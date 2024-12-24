<?php

namespace packages\adapter\view\authentication\login\blade;

use packages\adapter\presenter\authentication\login\blade\BladeLoginPresenter;

class BladeLoginView
{
    private BladeLoginPresenter $presenter;

    public function __construct(BladeLoginPresenter $presenter)
    {
        $this->presenter = $presenter;
    }

    public function response()
    {
        if ($this->presenter->isLoginSucceeded()) {
            return $this->successResponse();
        }

        return $this->faildResponse();
    }

    private function successResponse()
    {
        return redirect($this->presenter->successResponse());
    }

    private function faildResponse()
    {
        return redirect()
                ->back()
                ->withErrors($this->presenter->faildResponse())
                ->withInput();
    }
}