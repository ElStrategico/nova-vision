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
     * @var string
     */
    private string $targetModel;

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
     * @param string $targetModel
     */
    public function __construct(IConnect $connect, string $targetTable, string $targetModel)
    {
        $this->query = new Query();
        $this->connect = $connect;
        $this->targetModel = $targetModel;
        $this->targetTable = $targetTable;
    }

    private function build()
    {
        $sql = $this->query->getSelect();
        $sql .= " " . $this->query->getWhere();

        return $sql;
    }

    /**
     * @param array $columns
     * @return QueryBuilder
     */
    public function select($columns = [Aliases::ALL])
    {
        $implodedColumns = implode(', ', $columns);
        $this->query->setSelect($implodedColumns, $this->targetTable);

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
     * @param string $column
     * @return mixed
     */
    public function max(string $column)
    {
        $this->query->setMaxAggregation($column, $this->targetTable);
        return $this->connect->scalarFetch($this->build());
    }

    /**
     * @return Collection
     */
    public function get()
    {
        if(!$this->query->getSelect())
        {
            $this->query->setSelect(Aliases::ALL, $this->targetTable);
        }

        return FactoryCollection::factory(
            $this->targetModel,
            $this->connect->fetch($this->build(), $this->preparedParams)
        );
    }
}