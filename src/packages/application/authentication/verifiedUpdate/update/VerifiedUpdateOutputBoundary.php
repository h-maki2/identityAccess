<?php

namespace packages\application\authentication\verifiedUpdate\update;

abstract class  VerifiedUpdateOutputBoundary
{
    abstract public function formatForResponse(VerifiedUpdateResult $result): void;
    abstract public function formatForResponse();
}