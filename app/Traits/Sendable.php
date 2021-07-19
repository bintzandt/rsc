<?php

namespace App\Traits;

trait Sendable
{
    abstract function toFormBody(): array;
}
