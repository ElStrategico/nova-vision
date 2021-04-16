<?php


namespace NovaVision\Database;

use PDO;
use PDOStatement;
use NovaVision\Database\IConnect;

/**
 * Class Connect
 * @package NovaVision\Database
 * @author Artem Tyutnev <strategico.dev@gmail.com>
 */
class Connect implements IConnect
{
    /**
     * @var PDO
     */
    private PDO $pdo;

    /**
     * @param string $dsn
     * @param string $user
     * @param string $password
     */
    public function __construct(string $dsn, string $user, string $password)
    {
        $this->pdo = new PDO($dsn, $user, $password);
    }

    /**
     * Execute a SQL command
     *
     * @param string $sql
     * @param array $prepareParams
     * @return bool
     */
    public function execute(string $sql, array $prepareParams = []): bool
    {
        /* @var PDOStatement $state */
        $state = $this->pdo->prepare($sql);
        return $state->execute($prepareParams);
    }

    /**
     * Fetch a data from database by SQL command
     *
     * @param string $sql
     * @param array $prepareParams
     * @return array|null
     */
    public function fetch(string $sql, array $prepareParams = [])
    {
        /* @var PDOStatement $state */
        $state = $this->pdo->prepare($sql);
        $state->execute($prepareParams);

        return $state->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchOne(string $sql, array $prepareParams = [])
    {
        $state = $this->pdo->prepare($sql);
        $state->execute($prepareParams);

        return $state->fetch(PDO::FETCH_ASSOC);
    }

    public function scalarFetch(string $sql, array $prepareParams = [])
    {
        $state = $this->pdo->prepare($sql);
        $state->execute($prepareParams);

        return $state->fetchColumn();
    }
}