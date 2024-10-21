<?php

namespace packages\adapter\token\FirebaseJwtCreator;

use packages\domain\service\common\tokne\JwtCreator;

class FirebaseJwtCreator extends JwtCreator
{
    protected function payload(string $id): array
    {
        return [
            'iss' => config('app.app_domain'),
            'aud' => config('app.app_domain'),
        ];
    }
}