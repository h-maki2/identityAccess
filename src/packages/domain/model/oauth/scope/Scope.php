<?php

namespace packages\domain\model\oauth\scope;

enum Scope: string
{
    case ReadAccount = 'read_account';
    case EditAccount = 'edit_account';
    case DeleteAccount = 'delete_account';
}