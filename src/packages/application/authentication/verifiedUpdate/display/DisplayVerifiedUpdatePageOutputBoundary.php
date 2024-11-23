<?php

namespace packages\application\authentication\verifiedUpdate\display;

abstract class DisplayVerifiedUpdatePageOutputBoundary
{
    abstract public function present(DisplayVerifiedUpdatePageResult $result): void;
    abstract public function response();
}