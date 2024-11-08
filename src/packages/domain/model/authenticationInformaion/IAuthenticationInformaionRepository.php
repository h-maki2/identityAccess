<?php

namespace packages\domain\model\authenticationInformaion;

interface IAuthenticationInformaionRepository
{
    public function findByEmail(UserEmail $email): ?AuthenticationInformaion;

    public function findById(UserId $id): ?AuthenticationInformaion;

    public function save(AuthenticationInformaion $authenticationInformaion): void;

    public function delete(UserId $id): void;

    public function nextUserId(): UserId;
}