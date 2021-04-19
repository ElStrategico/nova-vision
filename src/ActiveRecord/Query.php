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
    private string $select = '';

    /**
     * @var string
     */
    private string $update = '';

    /**
     * @var string
     */
    private string $delete = '';

    /**
     * @var array
     */
    private array $joins = [];

    /**
     * @var array
     */
    private array $where = [];

    /**
     * @var string
     */
    private string $groupBy = '';

    /**
     * @var string
     */
    private string $orderBy = '';

    /**
     * @var string
     */
    private string $limit = '';

    /**
     * @return string
     */
    public function getAsString()
    {
        $sql = $this->getUpdate() ? $this->getUpdate() :  $this->getSelect();
        if($this->getDelete())
        {
            $sql = $this->getDelete();
        }

        $sql .= " " . $this->getJoins();
        $sql .= " " . $this->getGroupBy();
        $sql .= " " . $this->getWhere();
        $sql .= " " . $this->getOrderBy();
        $sql .= " " . $this->getLimit();

        return $sql;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getAsString();
    }

    /**
     * @return string
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @param string $columns
     * @param string $table
     */
    public function setSelect(string $columns, string $table)
    {
        $this->select = "SELECT $columns FROM $table";
    }

    public function clearSelect()
    {
        $this->select = '';
    }

    /**
     * @return string
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * @param string $table
     * @param string $data
     */
    public function setUpdate(string $table, string $data)
    {
        $this->update = "UPDATE $table SET $data";
    }

    /**
     * @return string
     */
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * @param string $table
     */
    public function setDelete(string $table)
    {
        $this->delete = "DELETE FROM $table";
    }

    /**
     * @return string
     */
    public function getWhere()
    {
        return implode(' ', $this->where);
    }

    /**
     * @param string $column
     * @param string $operator
     * @param mixed $value
     */
    public function setWhere(string $column, string $operator, $value)
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
     * @return string
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * @param string $column
     */
    public function setGroupBy(string $column)
    {
        $this->groupBy = "GROUP BY $column";
    }

    public function getJoins()
    {
        return implode(' ', $this->joins);
    }

    /**
     * @param string $type
     * @param string $table
     * @param string $column
     * @param string $operator
     * @param string $foreignColumn
     */
    public function setJoin(
        string $type,
        string $table,
        string $column,
        string $operator,
        string $foreignColumn
    )
    {
        $this->joins[] = "$type JOIN $table ON $column $operator $foreignColumn";
    }

    /**
     * @param string $table
     * @param string $column
     * @param string $operator
     * @param string $foreignColumn
     */
    public function setInnerJoin(string $table, string $column, string $operator, string $foreignColumn)
    {
        $this->setJoin('INNER', $table, $column, $operator, $foreignColumn);
    }

    /**
     * @param string $table
     * @param string $column
     * @param string $operator
     * @param string $foreignColumn
     */
    public function setLeftJoin(string $table, string $column, string $operator, string $foreignColumn)
    {
        $this->setJoin('LEFT', $table, $column, $operator, $foreignColumn);
    }

    /**
     * @param string $table
     * @param string $column
     * @param string $operator
     * @param string $foreignColumn
     */
    public function setRightJoin(string $table, string $column, string $operator, string $foreignColumn)
    {
        $this->setJoin('RIGHT', $table, $column, $operator, $foreignColumn);
    }

    /**
     * @return string
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = "LIMIT $limit";
    }

    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param string $column
     * @param string $type
     */
    public function setOrderBy(string $column, string $type)
    {
        $this->orderBy = "ORDER BY $column $type";
    }

    /**
     * @param string $function
     * @param string $aggregatable
     * @param string $table
     */
    public function setAggregationFunction(string $function, string $aggregatable, string $table)
    {
        $this->setSelect("$function($aggregatable)", $table);
    }

    /**
     * @param string $aggregatable
     * @param string $table
     */
    public function setMaxAggregation(string $aggregatable, string $table)
    {
        $this->setAggregationFunction("MAX", $aggregatable, $table);
    }

    /**
     * @param string $aggregatable
     * @param string $table
     */
    public function setMinAggregation(string $aggregatable, string $table)
    {
        $this->setAggregationFunction("MIN", $aggregatable, $table);
    }

    /**
     * @param string $aggregatable
     * @param string $table
     */
    public function setAvgAggregation(string $aggregatable, string $table)
    {
        $this->setAggregationFunction("AVG", $aggregatable, $table);
    }

    /**
     * @param string $aggregatable
     * @param string $table
     */
    public function setSumAggregation(string $aggregatable, string $table)
    {
        $this->setAggregationFunction("SUM", $aggregatable, $table);
    }

    /**
     * @param string $aggregatable
     * @param string $table
     */
    public function setCountAggregation(string $aggregatable, string $table)
    {
        $this->setAggregationFunction("COUNT", $aggregatable, $table);
    }

    /**
     * @param string $table
     * @param string $columns
     * @param string $values
     * @return string
     */
    public static function insertPreset(string $table, string $columns, string $values)
    {
        return "INSERT INTO $table ($columns) VALUES ($values)";
    }
}