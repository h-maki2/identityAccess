<?php

namespace packages\test\helpers\authenticationAccount;

use Faker\Factory as FakerFactory;
use packages\domain\model\authenticationAccount\UserEmail;

class TestUserEmailFactory
{
    public static function create(): UserEmail
    {
        $faker = FakerFactory::create();
        return new UserEmail($faker->email);
    }
}