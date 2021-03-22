<?php


namespace NovaVision\Configurations;

use Dotenv\Dotenv;

class EnvConfig implements IConfig
{
    /**
     * @var Dotenv
     */
    private Dotenv $dotEnv;

    public function __construct()
    {
        $this->dotEnv = Dotenv::createImmutable(__DIR__);
    }

    /**
     * @param string $key
     * @param null $default
     * @return string
     */
    public function get(string $key, $default = null): string
    {
        return $_ENV[$key] ?? $default;
    }
}