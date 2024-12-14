<?php

namespace packages\test\helpers\authenticationInformation;

use Faker\Factory as FakerFactory;
use packages\domain\model\authenticationInformation\UserEmail;

class TestUserEmailFactory
{
    public static function create(): UserEmail
    {
        $faker = FakerFactory::create();
        return new UserEmail($faker->email);
    }
}