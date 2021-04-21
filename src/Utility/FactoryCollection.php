<?php

namespace NovaVision\Utility;

use NovaVision\ActiveRecord\Collection;

class FactoryCollection
{
    /**
     * @param string $modelNamespace
     * @param array $rows
     * @return Collection
     */
    public static function factory(string $modelNamespace, array $rows)
    {
        $collection = new Collection();

        foreach($rows as $row)
        {
            $collection->add(FactoryModel::factory($modelNamespace, $row));
        }

        return $collection;
    }
}