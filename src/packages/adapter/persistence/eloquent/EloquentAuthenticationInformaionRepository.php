<?php

namespace packages\adapter\persistence\eloquent;

use App\Models\AuthenticationInformation as EloquentAuthenticationInformation;
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
        $result = EloquentAuthenticationInformation::where('email', $email->value)->first();

        if ($result === null) {
            return null;
        }

        return $this->toAuthenticationInformation($result);
    }

    public function findById(UserId $id): ?AuthenticationInformation
    {
        $result = $this->eloquentAuthenticationInformationFrom($id);

        if ($result === null) {
            return null;
        }

        return $this->toAuthenticationInformation($result);
    }

    public function save(AuthenticationInformation $authenticationInformation): void
    {
        $eloquentAuthenticationInformation = $this->toEloquentAuthenticationInformation($authenticationInformation);
        $eloquentAuthenticationInformation->save();
    }

    public function delete(UserId $id): void
    {
        $eloquentAuthenticationInformation = $this->eloquentAuthenticationInformationFrom($id);

        if ($eloquentAuthenticationInformation === null) {
            // 例外を発生させる予定
        }

        $eloquentAuthenticationInformation->delete();
    }

    private function toAuthenticationInformation(EloquentAuthenticationInformation $authenticationInformationObj): AuthenticationInformation
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

    private function toEloquentAuthenticationInformation(AuthenticationInformation $authenticationInformation): EloquentAuthenticationInformation
    {
        $eloquentAuthenticationInformation = $this->eloquentAuthenticationInformationFrom($authenticationInformation->id());

        if ($eloquentAuthenticationInformation === null) {
            $eloquentAuthenticationInformation = new EloquentAuthenticationInformation();
            $eloquentAuthenticationInformation->user_id = $authenticationInformation->id()->value;
        }

        $eloquentAuthenticationInformation->email = $authenticationInformation->email()->value;
        $eloquentAuthenticationInformation->password = $authenticationInformation->password()->hashedValue;
        $eloquentAuthenticationInformation->verification_status = $authenticationInformation->verificationStatus()->value;
        $eloquentAuthenticationInformation->failed_login_count = $authenticationInformation->loginRestriction()->failedLoginCount();
        $eloquentAuthenticationInformation->login_restriction_status = $authenticationInformation->loginRestriction()->loginRestrictionStatus();
        $eloquentAuthenticationInformation->next_login_allowed_at = $authenticationInformation->loginRestriction()->nextLoginAllowedAt();

        return $eloquentAuthenticationInformation;        
    }

    private function eloquentAuthenticationInformationFrom(UserId $userId): ?EloquentAuthenticationInformation
    {
        return EloquentAuthenticationInformation::find($userId->value);
    }
}