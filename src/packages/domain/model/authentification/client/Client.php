<?php


namespace packages\domain\model\authentification\client;

use League\OAuth2\Server\Entities\ClientEntityInterface;

class Client implements ClientEntityInterface
{
    private string $identifier;
    private string $name;
    private array|string $redirectUri;
    private bool $isConfidential;

    public function __construct(
        string $identifier,
        string $name,
        array|string $redirectUri,
        bool $isConfidential = true
    ) {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->redirectUri = $redirectUri;
        $this->isConfidential = $isConfidential;
    }

    /**
     * Get the client's identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
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