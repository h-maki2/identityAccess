<?php

namespace packages\domain\model\oauth\client;

class RedirectUrlList
{
    private array $valueList; // RedirectUrl[]

    public function __construct(string $redirectUrlStrings)
    {
        $this->setRedirectUrlList($redirectUrlStrings);
    }

    public function contains(RedirectUrl $redirectUrl): bool
    {
        foreach ($this->valueList as $value) {
            if ($value->equals($redirectUrl)) {
                return true;
            }
        }

        return false;
    }

    private function setRedirectUrlList(string $redirectUrlStrings) {
        $redirectUrlStringList = explode(',', $redirectUrlStrings);

        $redirectUrlList = [];
        foreach ($redirectUrlStringList as $redirectUrlString) {
            $redirectUrlList[] = new RedirectUrl($redirectUrlString);
        }

        $this->valueList = $redirectUrlList;
    }
}