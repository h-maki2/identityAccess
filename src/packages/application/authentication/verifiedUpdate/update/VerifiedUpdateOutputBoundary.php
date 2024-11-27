<?php

namespace packages\application\authentication\verifiedUpdate\update;

interface VerifiedUpdateOutputBoundary
{
    public function formatForResponse(VerifiedUpdateResult $result): void;
    public function response();
}