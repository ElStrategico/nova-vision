<?php


namespace NovaVision\ActiveRecord\Exceptions;

use Exception;
use Throwable;

class GuardException extends Exception
{
    public function __construct($guardedProperty)
    {
        parent::__construct(
            "Can not set a guarded property with name $guardedProperty",
            0,
            null
        );
    }
}