<?php

namespace packages\application\authentication\verifiedUpdate\update;

interface VerifiedUpdateInputBoundary
{
    public function verifiedUpdate(
        string $oneTimeTokenValueString,
        string $oneTimePasswordString
    ): VerifiedUpdateResult;
}