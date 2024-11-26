<?php

namespace packages\adapter\presenter\error;

interface ErrorPresenter
{
    public function formatForResponse(): void;
}