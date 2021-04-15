<?php


namespace NovaVision\Core;


abstract class BaseObject
{
    public function getClassName()
    {
        return get_class($this);
    }
}