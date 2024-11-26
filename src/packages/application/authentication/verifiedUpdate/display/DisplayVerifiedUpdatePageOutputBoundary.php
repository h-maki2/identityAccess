<?php

namespace packages\application\authentication\verifiedUpdate\display;

abstract class DisplayVerifiedUpdatePageOutputBoundary
{
    abstract public function formatForResponse(DisplayVerifiedUpdatePageResult $result): void;
    abstract public function response();
}