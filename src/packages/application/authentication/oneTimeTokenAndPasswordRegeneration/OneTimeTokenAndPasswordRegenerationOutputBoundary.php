<?php

namespace packages\application\authentication\oneTimeTokenAndPasswordRegeneration;

abstract class OneTimeTokenAndPasswordRegenerationOutputBoundary
{
    abstract public function present(OneTimeTokenAndPasswordRegenerationResult $oneTimeTokenAndPasswordRegenerationResult): void;
    abstract public function response();
}