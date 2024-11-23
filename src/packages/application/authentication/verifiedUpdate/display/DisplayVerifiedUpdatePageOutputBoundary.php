<?php

namespace packages\application\authentication\verifiedUpdate\display;

interface DisplayVerifiedUpdatePageOutputBoundary
{
    public function present(DisplayVerifiedUpdatePageResult $result): void;
}