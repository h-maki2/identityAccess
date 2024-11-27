<?php

namespace packages\application\authentication\oneTimeTokenAndPasswordRegeneration;

interface OneTimeTokenAndPasswordRegenerationOutputBoundary
{
    public function formatForResponse(OneTimeTokenAndPasswordRegenerationResult $oneTimeTokenAndPasswordRegenerationResult): void;
    public function response();
}