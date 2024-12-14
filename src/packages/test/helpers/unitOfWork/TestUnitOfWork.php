<?php

namespace packages\test\helpers\unitOfWork;

use packages\domain\model\common\unitOfWork\UnitOfWork;

class TestUnitOfWork extends UnitOfWork
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