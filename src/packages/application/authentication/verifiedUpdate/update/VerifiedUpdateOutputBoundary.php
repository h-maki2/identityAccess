<?php

namespace packages\application\authentication\verifiedUpdate\update;

abstract class  VerifiedUpdateOutputBoundary
{
    abstract public function present(VerifiedUpdateResult $result): void;
    abstract public function response();
}