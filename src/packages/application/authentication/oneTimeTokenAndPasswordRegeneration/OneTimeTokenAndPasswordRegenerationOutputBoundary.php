<?php

namespace packages\application\authentication\oneTimeTokenAndPasswordRegeneration;

interface OneTimeTokenAndPasswordRegenerationOutputBoundary
{
    public function present(OneTimeTokenAndPasswordRegenerationResult $oneTimeTokenAndPasswordRegenerationResult): void;
}