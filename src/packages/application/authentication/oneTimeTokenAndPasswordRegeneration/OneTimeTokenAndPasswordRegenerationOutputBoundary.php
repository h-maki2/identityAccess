<?php

namespace packages\application\authentication\oneTimeTokenAndPasswordRegeneration;

abstract class OneTimeTokenAndPasswordRegenerationOutputBoundary
{
    abstract public function formatForResponse(OneTimeTokenAndPasswordRegenerationResult $oneTimeTokenAndPasswordRegenerationResult): void;
    abstract public function response();
}