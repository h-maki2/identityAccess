<?php

namespace packages\adapter\view\registration\provisionalRegistration\blade;

use Illuminate\Contracts\View\View;
use packages\adapter\presenter\registration\provisionalRegistration\blade\BladeProvisionalRegistrationViewModel;

class BladeProvisionalRegistrationView
{
    private BladeProvisionalRegistrationViewModel $viewModel;

    public function __construct(BladeProvisionalRegistrationViewModel $viewModel)
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
        return view('registration.provisionalRegistration.provisionalRegistrationComplete');
    }

    private function faildResponse()
    {
        return redirect('/provisionalRegister')
                ->withErrors($this->viewModel->validationErrorList)
                ->withInput();
    }
}