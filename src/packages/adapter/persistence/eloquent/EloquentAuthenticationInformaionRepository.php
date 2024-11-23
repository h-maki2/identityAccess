<?php

namespace packages\adapter\persistence\eloquent;

use App\Models\AuthenticationInformation;
use DateTimeImmutable;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;
use packages\domain\model\AuthenticationInformation\LoginRestriction;
use packages\domain\model\AuthenticationInformation\FailedLoginCount;
use packages\domain\model\AuthenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\AuthenticationInformation\NextLoginAllowedAt;
use packages\domain\model\AuthenticationInformation\UserEmail;
use packages\domain\model\AuthenticationInformation\UserId;
use packages\domain\model\AuthenticationInformation\UserPassword;
use packages\domain\model\AuthenticationInformation\AuthenticationInformation;
use packages\domain\model\AuthenticationInformation\LoginRestrictionStatus;
use packages\domain\model\AuthenticationInformation\VerificationStatus;
use Ramsey\Uuid\Uuid;

class EloquentAuthenticationInformationRepository implements IAuthenticationInformationRepository
{
    public function findByEmail(UserEmail $email): ?AuthenticationInformation
    {

    }

    private function toAuthenticationInformation(AuthenticationInformation $authenticationInformationObj): AuthenticationInformation
    {
        return AuthenticationInformation::reconstruct(
            new UserId(new IdentifierFromUUIDver7(), $authenticationInformationObj->user_id),
            new UserEmail($authenticationInformationObj->email),
            UserPassword::reconstruct($authenticationInformationObj->password),
            VerificationStatus::from($authenticationInformationObj->verification_status),
            LoginRestriction::reconstruct(
                FailedLoginCount::reconstruct($authenticationInformationObj->failed_login_count),
                LoginRestrictionStatus::from($authenticationInformationObj->login_restriction_status),
                $authenticationInformationObj->next_login_allowed_at !== null ? NextLoginAllowedAt::reconstruct(new DateTimeImmutable($authenticationInformationObj->next_login_allowed_at)) : null
            )
        );
    }
}