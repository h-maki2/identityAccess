<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use packages\test\helpers\client\ClientTestDataCreator;
use Tests\TestCase;

class LaravelPassportAccessTokenTest extends TestCase
{
    private ClientTestDataCreator $clientTestDataCreator;

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->clientTestDataCreator = new ClientTestDataCreator();
    }
}