<?php

namespace packages\adapter\persistence\eloquent;

use App\Models\AuthenticationInformation as EloquentAuthenticationInformation;
use App\Models\User as EloquentUser;
use DateTimeImmutable;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;
use packages\domain\model\authenticationAccount\LoginRestriction;
use packages\domain\model\authenticationAccount\FailedLoginCount;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\NextLoginAllowedAt;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\AuthenticationAccount;
use packages\domain\model\authenticationAccount\LoginRestrictionStatus;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\DefinitiveRegistrationCompletedStatus;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use UnexpectedValueException;

class EloquentAuthenticationAccountRepository implements IAuthenticationAccountRepository
{
    public function findByEmail(UserEmail $email): ?AuthenticationAccount
    {
        $authInfoModel = EloquentAuthenticationInformation::where('email', $email->value)->first();

        if ($authInfoModel === null) {
            return null;
        }

        return $this->toAuthenticationAccount($authInfoModel, $authInfoModel->user);
    }

    public function findById(UserId $id, UnsubscribeStatus $unsubscribeStatus): ?AuthenticationAccount
    {
        $userModel = EloquentUser::where('id', $id->value)
                               ->where('unsubscribe', $unsubscribeStatus->value)
                               ->first();

        if ($userModel === null) {
            return null;
        }

        return $this->toAuthenticationAccount($userModel->authenticationInformation, $userModel);
    }

    public function save(AuthenticationAccount $authenticationAccount): void
    {
        EloquentUser::updateOrCreate(
            ['id' => $authenticationAccount->id()->value],
            ['unsubscribe' => $authenticationAccount->unsubscribeStatus()->value]
        );

        EloquentAuthenticationInformation::updateOrCreate(
            ['user_id' => $authenticationAccount->id()->value],
            [
                'email' => $authenticationAccount->email()->value,
                'password' => $authenticationAccount->password()->hashedValue,
                'verification_status' => $authenticationAccount->DefinitiveRegistrationCompletedStatus()->value,
                'failed_login_count' => $authenticationAccount->loginRestriction()->failedLoginCount(),
                'login_restriction_status' => $authenticationAccount->loginRestriction()->loginRestrictionStatus(),
                'next_login_allowed_at' => $authenticationAccount->loginRestriction()->nextLoginAllowedAt()
            ]
        );
    }

    public function delete(AuthenticationAccount $authenticationAccount): void
    {
        $userModel = EloquentUser::find($authenticationAccount->id()->value);

        if ($userModel === null) {
            throw new UnexpectedValueException('指定されたIDのユーザーは存在しません。');
        }

        $userModel->unsubscribe = $authenticationAccount->unsubscribeStatus()->value;
        $userModel->authenticationInformation->delete();
        $userModel->save();
    }

    public function nextUserId(): UserId
    {
        return new UserId(Uuid::uuid7());
    }

    private function toAuthenticationAccount(
        EloquentAuthenticationInformation $eloquentAuthenticationInformation,
        EloquentUser $eloquentUser
    ): AuthenticationAccount
    {
        return AuthenticationAccount::reconstruct(
            new UserId($eloquentUser->id),
            new UserEmail($eloquentAuthenticationInformation->email),
            UserPassword::reconstruct($eloquentAuthenticationInformation->password),
            DefinitiveRegistrationCompletedStatus::from($eloquentAuthenticationInformation->verification_status),
            LoginRestriction::reconstruct(
                FailedLoginCount::reconstruct($eloquentAuthenticationInformation->failed_login_count),
                LoginRestrictionStatus::from($eloquentAuthenticationInformation->login_restriction_status),
                $eloquentAuthenticationInformation->next_login_allowed_at !== null ? NextLoginAllowedAt::reconstruct(new DateTimeImmutable($eloquentAuthenticationInformation->next_login_allowed_at)) : null
            ),
            UnsubscribeStatus::from($eloquentUser->unsubscribe)
        );
    }
}