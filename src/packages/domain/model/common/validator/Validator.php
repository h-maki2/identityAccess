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
     * エラーメッセージを取得
     */
    public function errorMessageList(): array
    {
        return $this->errorMessageList;
    }

    /**
     * バリデーション対象のフィールド名を取得
     */
    abstract public function fieldName(): string;

    /**
     * エラーメッセージをセット
     */
    protected function setErrorMessage(string $message): void
    {
        $this->errorMessageList[] = $message;
    }
}