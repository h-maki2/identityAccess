<?php

namespace packages\adapter\presenter\common\json;

enum HttpStatus: string
{
    case Success = '200';
    case NoContent = '204';
    case BadRequest = '400';
    case Unauthorized = '401';
    case InternalServerError = '500';

    public function code(): string
    {
        return match ($this) {
            self::Success => 'Success',
            self::BadRequest => 'Bad Request',
            self::Unauthorized => 'Unauthorized',
            self::InternalServerError => 'Internal Server Error',
            self::NoContent => 'No Content',
        };
    }

    public function isSuccess(): bool
    {
        return $this === self::Success;
    }
}