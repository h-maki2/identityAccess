<?php

namespace packages\domain\model\oauth\scope;

class ScopeList
{
    private array $value;

    /**
     * @param Scope[] $scopes
     */
    private function __construct(array $scopes)
    {
        $this->value = $scopes;
    }

    public static function createFromString(string $scopes): self
    {
        $scopeList = [];
        foreach (self::scopeStringList($scopes) as $scopeString) {
            $scopeList[] = Scope::from($scopeString);
        }

        return new self($scopeList);
    }

    public function stringValue(): string
    {
        return implode(' ', $this->value);
    }

    private static function scopeStringList(string $scopeString): array
    {
        return explode(' ', $scopeString);
    }
}