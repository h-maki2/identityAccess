<?php

namespace packages\adapter\validator;

use Illuminate\Support\Facades\Validator as LaravelValidatorFactory;
use Illuminate\Validation\Validator as LaravelValidator;
use packages\domain\model\common\validator\ValidationHandler;
use packages\domain\model\common\validator\Validator;
use RuntimeException;

class LaravelValidationHandler extends ValidationHandler
{
    private LaravelValidator $laravelValidator;

    public function validate(): bool
    {
        $laravelValidator = LaravelValidatorFactory::make($this->validatorList(), $this->execValidationList());
        $this->setLaravelValidator($laravelValidator);
        return $laravelValidator->passes();
    }

    public function getValidationError(): LaravelValidator
    {
        if ($this->laravelValidator === null) {
            throw new RuntimeException('バリデーションが実行されていません。');
        }

        return $this->laravelValidator;
    }

    private function execValidationList(): array
    {
        $execValidationList = [];
        foreach ($this->validatorList() as $validator) {
            $execValidationList[$validator->fieldName()] = [
                function (string  $attribute, Validator $validator, callable $fail) {
                    if (!$validator->validate()) {
                        foreach ($validator->errorMessageList() as $errorMessage) {
                            $fail($errorMessage);
                        }
                    }
                },
            ];
        }
        return $execValidationList;
    }

    private function setLaravelValidator(LaravelValidator $laravelValidator): void
    {
        $this->laravelValidator = $laravelValidator;
    }
}