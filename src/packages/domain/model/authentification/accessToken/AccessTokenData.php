<?php

namespace packages\domain\model\authentification\client;

use DateTimeImmutable;
use League\OAuth2\Server\CryptKeyInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use packages\domain\model\authTokenManage\AccessTokenExpirationDate;
use packages\domain\model\userProfile\UserId;
use packages\domain\service\common\tokne\JwtTokenHandler;

class AccessTokenData implements AccessTokenEntityInterface
{
    private CryptKeyInterface $privateKey;
    private DateTimeImmutable $expirationDate;
    private JwtTokenHandler $jwtTokenHandler;
    private string $userId;
    private Client $client;
    private string $tokenId;
    private array $scope;

    public function __construct(
        CryptKeyInterface $privateKey,
        AccessTokenExpirationDate $expirationDate, 
        UserId $userId,
        JwtTokenHandler $jwtTokenHandler,
        Client $client,
        string $tokenId,
        array $scope
    )
    {
        $this->setPrivateKey($privateKey);
        $this->setExpiryDateTime($expirationDate->value);
        $this->setUserIdentifier($userId->value);
        $this->setClient($client);
        $this->setIdentifier($tokenId);
        $this->setScope($scope);
        $this->jwtTokenHandler = $jwtTokenHandler;
    }

    public function setPrivateKey(CryptKeyInterface $privateKey): void
    {
        $this->privateKey = $privateKey;
    }

    public function privateKeyPath(): string
    {
        return $this->privateKey->getKeyPath();
    }

    /**
     * userIDをjwtを使ってトークン化する
     */
    public function toString(): string
    {
        return $this->jwtTokenHandler->encode(
            $this->userId, 
            $this->expirationDate->getTimestamp(),
            $this->privateKeyPath()
        );
    }

    /**
     * Get the token's identifier.
     *
     * @return non-empty-string
     */
    public function getIdentifier(): string
    {
        return $this->tokenId;
    }

    /**
     * Set the token's identifier.
     *
     * @param non-empty-string $tokenId
     */
    public function setIdentifier(string $tokenId): void
    {
        $this->tokenId = $tokenId;
    }

    /**
     * Get the token's expiry date time.
     */
    public function getExpiryDateTime(): DateTimeImmutable
    {
        return $this->expirationDate;
    }

    /**
     * Set the date time when the token expires.
     */
    public function setExpiryDateTime(DateTimeImmutable $dateTime): void
    {
        $this->expirationDate = $dateTime;
    }

    /**
     * Set the identifier of the user associated with the token.
     *
     * @param non-empty-string $identifier
     */
    public function setUserIdentifier(string $identifier): void
    {
        $this->userId = $identifier;
    }

    /**
     * Get the token user's identifier.
     *
     * @return non-empty-string|null
     */
    public function getUserIdentifier(): string|null
    {
        return $this->userId;
    }

    /**
     * Get the client that the token was issued to.
     */
    public function getClient(): ClientEntityInterface
    {
        return $this->client;
    }

    /**
     * Set the client that the token was issued to.
     */
    public function setClient(ClientEntityInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * Associate a scope with the token.
     */
    public function addScope(ScopeEntityInterface $scope): void
    {
        $this->scope[] = $scope;
    }

    public function setScope(array $scopeList): void
    {
        foreach ($scopeList as $scope) {
            $this->addScope($scope);
        }
    }

    /**
     * Return an array of scopes associated with the token.
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes(): array
    {
        return $this->scope;
    }
}