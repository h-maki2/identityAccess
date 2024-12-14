<?php

namespace packages\adapter\unitOfWork;

use Illuminate\Support\Facades\DB;
use packages\domain\model\common\unitOfWork\UnitOfWork;

class EloquentUnitOfWork extends UnitOfWork
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