<?php

namespace packages\domain\model\oauth\authToken;

interface IAccessTokenCookieService
{
    public function fetch(): ?AccessToken;
}