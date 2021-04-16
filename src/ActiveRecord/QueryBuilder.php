<?php


namespace NovaVision\ActiveRecord;

use NovaVision\Database\IConnect;
use NovaVision\ActiveRecord\Query;
use NovaVision\Utility\FactoryModel;
use NovaVision\Utility\FactoryCollection;

/**
 * Class QueryBuilder
 * @package NovaVision\ActiveRecord
 * @author Artem Tyutnev <strategico.dev@gmail.com>
 */
class QueryBuilder
{
    /**
     * @var IConnect
     */
    private IConnect $connect;

    /**
     * @var Query
     */
    private Query $query;

    /**
     * @var mixed
     */
    private $targetModel;

    /**
     * @var string
     */
    private string $targetTable;

    /**
     * @var array
     */
    private array $preparedParams = [];

    /**
     * @param IConnect $connect
     * @param string $targetTable
     * @param mixed $targetModel
     */
    public function __construct(IConnect $connect, string $targetTable, $targetModel = null)
    {
        $this->query = new Query();
        $this->connect = $connect;
        $this->targetModel = $targetModel;
        $this->targetTable = $targetTable;
    }

    private function beforeBuild()
    {
        if(!$this->query->getSelect())
        {
            $this->query->setSelect(Aliases::ALL, $this->targetTable);
        }
    }

    /**
     * @return string
     */
    private function build()
    {
        $this->beforeBuild();

        return $this->query->getAsString();
    }

    /**
     * @param array $columns
     * @return QueryBuilder
     */
    public function select(array $columns = [Aliases::ALL])
    {
        $implodedColumns = implode(', ', $columns);
        $this->query->setSelect($implodedColumns, $this->targetTable);

        return $this;
    }

    /**
     * @param string $table
     * @param string $column
     * @param string $operator
     * @param string $foreignColumn
     * @return $this
     */
    public function innerJoin(string $table, string $column, string $operator, string $foreignColumn)
    {
        $this->query->setInnerJoin($table, $column, $operator, $foreignColumn);

        return $this;
    }

    /**
     * @param string $table
     * @param string $column
     * @param string $operator
     * @param string $foreignColumn
     * @return $this
     */
    public function leftJoin(string $table, string $column, string $operator, string $foreignColumn)
    {
        $this->query->setLeftJoin($table, $column, $operator, $foreignColumn);

        return $this;
    }

    /**
     * @param string $table
     * @param string $column
     * @param string $operator
     * @param string $foreignColumn
     * @return $this
     */
    public function rightJoin(string $table, string $column, string $operator, string $foreignColumn)
    {
        $this->query->setRightJoin($table, $column, $operator, $foreignColumn);

        return $this;
    }

    /**
     * @param string $column
     * @param string $operator
     * @param string $value
     * @return $this
     */
    public function where(string $column, string $operator, string $value)
    {
        $this->query->setWhere($column, $operator, Aliases::PREPARED_PLACE);
        $this->preparedParams[] = $value;

        return $this;
    }

    /**
     * @param string $column
     * @param string $operator
     * @param string $value
     * @return $this
     */
    public function orWhere(string $column, string $operator, string $value)
    {
        $this->query->setOrWhere($column, $operator, Aliases::PREPARED_PLACE);
        $this->preparedParams[] = $value;

        return $this;
    }

    /**
     * @param string $column
     * @param string $operator
     * @param string $value
     * @return $this
     */
    public function andWhere(string $column, string $operator, string $value)
    {
        $this->query->setAndWhere($column, $operator, Aliases::PREPARED_PLACE);
        $this->preparedParams[] = $value;

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->query->setLimit($limit);

        return $this;
    }

    /**
     * @param string $column
     * @param string $sortType
     * @return $this
     */
    public function orderBy(string $column, string $sortType = Aliases::ASC_SORT)
    {
        $this->query->setOrderBy($column, $sortType);

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function groupBy(string $column)
    {
        $this->query->setGroupBy($column);

        return $this;
    }

    /**
     * @param string $column
     * @return mixed
     */
    public function max(string $column)
    {
        $this->query->setMaxAggregation($column, $this->targetTable);
        return $this->connect->scalarFetch($this->build());
    }

    /**
     * @param string $column
     * @return mixed
     */
    public function min(string $column)
    {
        $this->query->setMinAggregation($column, $this->targetTable);
        return $this->connect->scalarFetch($this->build());
    }

    /**
     * @param string $column
     * @return mixed
     */
    public function avg(string $column)
    {
        $this->query->setAvgAggregation($column, $this->targetTable);
        return $this->connect->scalarFetch($this->build());
    }

    /**
     * @param string $column
     * @return mixed
     */
    public function sum(string $column)
    {
        $this->query->setSumAggregation($column, $this->targetTable);
        return $this->connect->scalarFetch($this->build());
    }

    /**
     * @param string $column
     * @return mixed
     */
    public function count(string $column)
    {
        $this->query->setCountAggregation($column, $this->targetTable);
        return $this->connect->scalarFetch($this->build());
    }

    /**
     * @return $this
     */
    public function asArray()
    {
        $this->targetModel = null;

        return $this;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        $records = $this->connect->fetch($this->build(), $this->preparedParams);
        if(!$this->targetModel)
        {
            return $records;
        }

        return FactoryCollection::factory(
            $this->targetModel,
            $records
        );
    }

    /**
     * @return mixed|null
     */
    public function first()
    {
        if(!$record = $this->connect->fetchOne($this->build(), $this->preparedParams))
        {
            return null;
        }
        if(!$this->targetModel)
        {
            return $record;
        }

        return FactoryModel::factory(
            $this->targetModel,
            $record
        );
    }
}