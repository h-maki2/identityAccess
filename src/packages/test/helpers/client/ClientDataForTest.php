<?php

namespace packages\test\helpers\client;

use packages\domain\model\client\ClientData;

class ClientDataForTest extends ClientData
{
    public function urlForObtainingAuthorizationCode(): string
    {
        return 'http://localhost:8080' . $this->urlPathForObtainingAuthorizationCode();
    }
}