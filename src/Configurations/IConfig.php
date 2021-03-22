<?php


namespace NovaVision\Configurations;

/**
 * Interface IConfig
 * @package NovaVision\Configurations
 * @author Artem Tyutnev <strategico.dev@gmail.com>
 */
interface IConfig
{
    /**
     * @param string $key
     * @param string|null $default
     * @return string mixed
     */
    public function get(string $key, $default = null) : string;
}