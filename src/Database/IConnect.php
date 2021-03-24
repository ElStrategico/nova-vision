<?php


namespace NovaVision\Database;

/**
 * Interface IConnect
 * @package NovaVision\Database
 * @author Artem Tyutnev <strategico.dev@gmail.com>
 */
interface IConnect
{
    /**
     * @param string $dsn
     * @param string $user
     * @param string $password
     */
    public function __construct(string $dsn, string $user, string $password);

    /**
     * Execute a SQL command
     *
     * @param string $sql
     * @param array $prepareParams
     * @return bool
     */
    public function execute(string $sql, array $prepareParams = []) : bool;

    /**
     * Fetch a data from database by SQL command
     *
     * @param string $sql
     * @param array $prepareParams
     * @return array|null
     */
    public function fetch(string $sql, array $prepareParams = []);
}