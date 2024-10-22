<?php

namespace packages\domain\model\authentification\client;

use League\OAuth2\Server\Entities\ClientEntityInterface;

class Client implements ClientEntityInterface
{
    private ClientId $id;
    private string $name;
    private array|string $redirectUri;
    private bool $isConfidential;

    public function __construct(
        ClientId $id,
        string $name,
        array|string $redirectUri,
        bool $isConfidential
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->redirectUri = $redirectUri;
        $this->isConfidential = $isConfidential;
    }

    /**
     * Get the client's identifier.
     */
    public function getIdentifier(): string
    {
        return $this->id->value;
    }

    /**
     * Get the client's name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the client's redirect URI(s).
     */
    public function getRedirectUri(): string|array
    {
        return $this->redirectUri;
    }

    /**
     * Returns true if the client is confidential.
     */
    public function isConfidential(): bool
    {
        return $this->isConfidential;
    }
}