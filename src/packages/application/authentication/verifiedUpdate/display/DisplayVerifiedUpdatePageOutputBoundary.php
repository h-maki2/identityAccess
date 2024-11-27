<?php

namespace packages\application\authentication\verifiedUpdate\display;

interface DisplayVerifiedUpdatePageOutputBoundary
{
    public function formatForResponse(DisplayVerifiedUpdatePageResult $result): void;
    public function response(): mixed;
}