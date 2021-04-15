<?php

namespace NovaVision\ActiveRecord;

/**
 * @package NovaVision\ActiveRecord
 * @author Artem Tyutnev <strategico.dev@gmail.com>
 */
class Query
{
    /**
     * @var string
     */
    private $select = '';

    /**
     * @var array
     */
    private $where = [];

    /**
     * @return string
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @return string
     */
    public function getWhere()
    {
        return implode(' ', $this->where);
    }

    /**
     * @param string $columns
     * @param string $table
     */
    public function setSelect(string $columns, string $table)
    {
        $this->select = "SELECT $columns FROM $table";
    }

    /**
     * @param string $column
     * @param string $operator
     * @param string $value
     */
    public function setWhere(string $column, string $operator, string $value)
    {
        if(count($this->where))
        {
            $this->setAndWhere($column, $operator, $value);
        }

        $this->where[] = "WHERE $column $operator $value";
    }

    /**
     * @param string $column
     * @param string $operator
     * @param string $value
     */
    public function setAndWhere(string $column, string $operator, string $value)
    {
        $this->where[] = "AND $column $operator $value";
    }

    /**
     * @param string $column
     * @param string $operator
     * @param string $value
     */
    public function setOrWhere(string $column, string $operator, string $value)
    {
        $this->where[] = "OR $column $operator $value";
    }
    /**
     * @param string $aggregatable
     * @param string $table
     */
    public function setMaxAggregation(string $aggregatable, string $table)
    {
        $this->setSelect("MAX($aggregatable)", $table);
    }
}