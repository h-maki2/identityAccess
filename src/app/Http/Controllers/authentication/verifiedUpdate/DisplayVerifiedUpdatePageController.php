<?php

namespace App\Http\Controllers\authentication\verifiedUpdate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageInputBoundary;

class DisplayVerifiedUpdatePageController extends Controller
{
    private DisplayVerifiedUpdatePageInputBoundary $displayVerifiedUpdatePageInputBoundary;

    public function __construct(DisplayVerifiedUpdatePageInputBoundary $displayVerifiedUpdatePageInputBoundary)
    {
        $this->displayVerifiedUpdatePageInputBoundary = $displayVerifiedUpdatePageInputBoundary;
    }

    public function displayVerifiedUpdatePage(Request $request): mixed
    {
        $output = $this->displayVerifiedUpdatePageInputBoundary->displayVerifiedUpdatePage(
            $request->query('oneTimeTokenValue')
        );

        return $output->response();
    }
}