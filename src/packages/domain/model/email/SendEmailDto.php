<?php

namespace packages\domain\model\email;

class SendEmailDto
{
    readonly string $fromAddress;
    readonly string $toAddress;
    readonly string $systemName;
    readonly string $subject;
    readonly string $mailFilePath;
    readonly array $templateVariables;

    public function __construct(
        string $fromAddress,
        string $toAddress,
        string $systemName,
        string $subject,
        string $mailFilePath,
        array $templateVariables
    )
    {
        $this->fromAddress = $fromAddress;
        $this->toAddress = $toAddress;
        $this->systemName = $systemName;
        $this->subject = $subject;
        $this->mailFilePath = $mailFilePath;
        $this->templateVariables = $templateVariables;
    }
}