<?php


namespace NovaVision\Configurations;


class MockConfig implements IConfig
{
    const MOCK_DRIVER   = 'mysql';
    const MOCK_HOST     = 'localhost';
    const MOCK_DATABASE = 'mock';
    const MOCK_USER     = 'root';
    const MOCK_PASSWORD = 'root';

    /**
     * @var array
     */
    private array $config = [
        'DRIVER'    => self::MOCK_DRIVER,
        'HOST'      => self::MOCK_HOST,
        'DATABASE'  => self::MOCK_DATABASE,
        'USER'      => self::MOCK_USER,
        'PASSWORD'  => self::MOCK_PASSWORD
    ];

    /**
     * @param string $key
     * @param null $default
     * @return string
     */
    public function get(string $key, $default = null): string
    {
        return $this->config[$key] ?? $default;
    }
}