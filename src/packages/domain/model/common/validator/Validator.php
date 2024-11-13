<?php

namespace packages\domain\model\common\validator;

abstract class Validator
{
    protected array $errorMessageList = [];

    /**
     * バリデーションを実行
     */
    abstract public function validate(): bool;

    /**
     * バリデーション対象のフィールド名を取得
     */
    abstract protected function fieldName(): string;

    /**
     * エラーメッセージを取得
     */
    protected function errorMessageList(): array
    {
        return $this->errorMessageList;
    }

    /**
     * エラーメッセージをセット
     */
    protected function setErrorMessage(string $message): void
    {
        $this->errorMessageList[$this->fieldName()][] = $message;
    }
}