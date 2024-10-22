<?php

namespace packages\domain\model\authentification\client;

use League\OAuth2\Server\Entities\ClientEntityInterface;

class Client implements ClientEntityInterface
{
    private ClientId $id;
    private ClientSecret $secret;
    private string $name;
    private array|string $redirectUri;
    private bool $isConfidential;

    public function __construct(
        ClientId $id,
        ClientSecret $secret,
        string $name,
        array|string $redirectUri,
        bool $isConfidential
    ) {
        $this->id = $id;
        $this->secret = $secret;
        $this->name = $name;
        $this->redirectUri = $redirectUri;
        $this->isConfidential = $isConfidential;
    }

    public function getIdentifier(): string
    {
        return $this->id->value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRedirectUri(): string|array
    {
        return $this->redirectUri;
    }

    public function isConfidential(): bool
    {
        return $this->isConfidential;
    }

    public function secret(): ClientSecret
    {
        return $this->secret;
    }
}