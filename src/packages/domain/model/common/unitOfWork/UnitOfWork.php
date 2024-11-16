<?php

namespace packages\domain\model\common\unitOfWork;

abstract class UnitOfWork
{
    abstract protected function beginTransaction(): void;
    abstract protected function commit(): void;
    abstract protected function rollback(): void;
    
    public function performTransaction(callable $transactionalFunction)
    {
        try {
            $this->beginTransaction();
            $transactionalFunction();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}