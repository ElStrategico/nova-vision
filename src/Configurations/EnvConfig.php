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
        $rootDir = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
        $this->dotEnv = Dotenv::createImmutable($rootDir);
        $this->dotEnv->load();
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