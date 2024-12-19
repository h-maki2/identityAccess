<?php

namespace packages\adapter\view\registration\UserProvisionalRegistration\blade;

use Illuminate\Contracts\View\View;
use packages\adapter\presenter\registration\UserProvisionalRegistration\blade\BladeUserProvisionalRegistrationViewModel;

class BladeUserProvisionalRegistrationView
{
    private BladeUserProvisionalRegistrationViewModel $viewModel;

    public function __construct(BladeUserProvisionalRegistrationViewModel $viewModel)
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
        return view('registration.provisionalRegistration.userProvisionalRegistrationComplete');
    }

    private function faildResponse()
    {
        return redirect()
                ->back()
                ->withErrors($this->viewModel->validationErrorList)
                ->withInput();
    }
}