<?php

namespace packages\application\authentication\verifiedUpdate\display;

interface DisplayVerifiedUpdatePageInputBoundary
{
    public function displayVerifiedUpdatePage(string $oneTimeTokenValueString): DisplayVerifiedUpdatePageOutputBoundary;
}