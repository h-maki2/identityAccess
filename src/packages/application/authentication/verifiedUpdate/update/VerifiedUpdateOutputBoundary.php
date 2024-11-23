<?php

namespace packages\application\authentication\verifiedUpdate\update;

interface VerifiedUpdateOutputBoundary
{
    public function present(VerifiedUpdateResult $result): void;
}