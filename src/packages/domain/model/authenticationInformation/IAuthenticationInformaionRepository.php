<?php

namespace packages\domain\model\AuthenticationInformation;

interface IAuthenticationInformationRepository
{
    public function findByEmail(UserEmail $email): ?AuthenticationInformation;

    public function findById(UserId $id): ?AuthenticationInformation;

    public function save(AuthenticationInformation $authenticationInformation): void;

    public function delete(UserId $id): void;

    public function nextUserId(): UserId;
}