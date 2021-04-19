<?php

namespace NovaVision\Utility;

class FactoryModel
{
    /**
     * @param string $modelNamespace
     * @param array $attributes
     * @return mixed
     */
    public static function factory(string $modelNamespace, array $attributes)
    {
        return new $modelNamespace($attributes, true);
    }
}