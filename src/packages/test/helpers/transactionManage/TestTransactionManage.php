<?php

namespace packages\test\helpers\transactionManage;

use packages\domain\model\common\transactionManage\TransactionManage;

class TestTransactionManage extends TransactionManage
{
    protected function beginTransaction(): void
    {
        // Do nothing
    }

    protected function commit(): void
    {
        // Do nothing
    }

    protected function rollback(): void
    {
        // Do nothing
    }
}