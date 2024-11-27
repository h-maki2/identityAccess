<?php

namespace packages\application\authentication\oneTimeTokenAndPasswordRegeneration;

interface OneTimeTokenAndPasswordRegenerationInputBoundary
{
    public function regenerateOneTimeTokenAndPassword(string $email): OneTimeTokenAndPasswordRegenerationOutputBoundary;
}