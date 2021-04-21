<?php
declare(strict_types = 1);

namespace NovaVision\Utility;

use NovaVision\Database\IConnect;
use NovaVision\Providers\ConnectData;

class FactoryConnect
{
    /**
     * @param string $configClass
     * @param string $connectClass
     * @return IConnect
     */
    public static function factory(string $configClass, string $connectClass) : IConnect
    {
        $connectData = new ConnectData(new $configClass);

        return new $connectClass(
            $connectData->getDSN(),
            $connectData->getUser(),
            $connectData->getPassword()
        );
    }
}