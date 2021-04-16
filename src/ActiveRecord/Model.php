<?php


namespace NovaVision\ActiveRecord;

use NovaVision\Core\BaseObject;
use NovaVision\Database\Connect;
use NovaVision\Utility\FactoryConnect;
use morphos\English\NounPluralization;
use NovaVision\Configurations\EnvConfig;
use NovaVision\ActiveRecord\QueryBuilder;

/**
 * Class Model
 * @package NovaVision\ActiveRecord
 * @author Artem Tyutnev <strategico.dev@gmail.com>
 */
abstract class Model extends BaseObject
{
    /**
     * @var string
     */
    protected string $table = '';

    /**
     * Properties for work with save, update methods
     *
     * @var array
     */
    protected array $properties = [];

    /**
     * Model constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        foreach($attributes as $column => $value)
        {
            $this->$column = $value;
        }
    }

    public function getTable()
    {
        if(!$this->table)
        {
            $this->table = NounPluralization::pluralize($this->getClassName());
        }

        return $this->table;
    }

    /**
     * @return \NovaVision\ActiveRecord\QueryBuilder
     */
    public static function query()
    {
        $modelInstance = new static;
    
        return new QueryBuilder(
            FactoryConnect::factory(EnvConfig::class, Connect::class),
            $modelInstance->getTable(),
            static::class
        );
    }
}