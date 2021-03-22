<?php


namespace NovaVision\Providers;

use NovaVision\Configurations\IConfig;

/**
 * Class ConnectData
 * @package NovaVision\Providers
 * @author Artem Tyutnev <strategico.dev@gmail.com>
 */
class ConnectData
{
    /**
     * @var IConfig
     */
    private IConfig $config;

    const DRIVER    = 'DRIVER';
    const HOST      = 'HOST';
    const DATABASE  = 'DATABASE';
    const USER      = 'USER';
    const PASSWORD  = 'PASSWORD';

    /**.
     * @param IConfig $config
     */
    public function __construct(IConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return string|null
     */
    public function getDriver()
    {
        return $this->config->get(self::DRIVER);
    }

    /**
     * @return string|null
     */
    public function getHost()
    {
        return $this->config->get(self::HOST);
    }

    /**
     * @return string|null
     */
    public function getDatabaseName()
    {
        return $this->config->get(self::DATABASE);
    }

    /**
     * @return string|null
     */
    public function getUser()
    {
        return $this->config->get(self::USER);
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->config->get(self::PASSWORD);
    }

    /**
     * @return string
     */
    public function getDSN()
    {
        $driver = $this->getDriver();
        $databaseName = $this->getDatabaseName();
        $host = $this->getHost();

        return "$driver:dbname=$databaseName;host=$host";
    }
}