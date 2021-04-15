<?php

namespace NovaVision\ActiveRecord;

use Traversable;
use ArrayIterator;
use IteratorAggregate;
use NovaVision\ActiveRecord\Model;

/**
 * @package NovaVision\Collection
 * @author Artem Tyutnev <strategico.dev@gmail.com>
 */
class Collection implements IteratorAggregate
{
    /**
     * @var array
     */
    private array $models = [];

    /**
     * @param array $models
     */
    public function __construct(array $models = [])
    {
        $this->models = $models;
    }

    /**
     * @param mixed $model
     */
    public function add($model)
    {
        $this->models[] = $model;
    }

    public function getSize()
    {
        return count($this->models);
    }

    public function set($key, $value)
    {
        $this->models[$key] = $value;
    }

    public function get($key)
    {
        return $this->models[$key];
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->models);
    }
}