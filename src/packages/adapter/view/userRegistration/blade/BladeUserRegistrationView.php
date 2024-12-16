<?php

namespace packages\adapter\view\userRegistration\blade;

use Illuminate\Contracts\View\View;
use packages\adapter\presenter\userRegistration\blade\BladeUserRegistrationViewModel;

class BladeUserRegistrationView
{
    private BladeUserRegistrationViewModel $viewModel;

    public function __construct(BladeUserRegistrationViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
    }

    public function response()
    {
        if ($this->viewModel->isValidationError) {
            return $this->faildResponse();
        }

        return $this->successResponse();
    }

    private function successResponse(): View
    {
        return view('userRegistration.userRegistrationComplete');
    }

    private function faildResponse()
    {
        return redirect()
                ->back()
                ->withErrors($this->viewModel->validationErrorList)
                ->withInput();
    }
}