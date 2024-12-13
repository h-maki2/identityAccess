<?php

namespace packages\domain\model\oauth\scope;

enum Scope: string
{
    case ViewUserProfile = 'view-profile';
    case RegisterUserProfile = 'register-profile';
    case EditUserProfile = 'edit-profile';
}