<?php

namespace packages\adapter\transactionManage;

use Illuminate\Support\Facades\DB;
use packages\domain\model\common\transactionManage\TransactionManage;

class EloquentTransactionManage extends TransactionManage
{
    protected function beginTransaction(): void
    {
        DB::beginTransaction();
    }

    protected function commit(): void
    {
        DB::commit();
    }

    protected function rollback(): void
    {
        DB::rollBack();
    }
}