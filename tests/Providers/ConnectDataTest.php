<?php


namespace Providers;

use PHPUnit\Framework\TestCase;
use NovaVision\Providers\ConnectData;
use NovaVision\Configurations\MockConfig;

class ConnectDataTest extends TestCase
{
    /**
     * @var ConnectData
     */
    private ConnectData $connectData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connectData = new ConnectData(new MockConfig());
    }

    public function testFetchDriver()
    {
        $expectDriver = MockConfig::MOCK_DRIVER;

        $currentDriver = $this->connectData->getDriver();

        $this->assertEquals($expectDriver, $currentDriver);
    }

    public function testFetchHost()
    {
        $expectHost = MockConfig::MOCK_HOST;

        $currentHost = $this->connectData->getHost();

        $this->assertEquals($expectHost, $currentHost);
    }

    public function testFetchDatabase()
    {
        $expectDatabase = MockConfig::MOCK_DATABASE;

        $currentDatabase = $this->connectData->getDatabaseName();

        $this->assertEquals($expectDatabase, $currentDatabase);
    }

    public function testFetchUser()
    {
        $expectUser = MockConfig::MOCK_USER;

        $currentUser = $this->connectData->getUser();

        $this->assertEquals($expectUser, $currentUser);
    }

    public function testFetchPassword()
    {
        $expectPassword = MockConfig::MOCK_PASSWORD;

        $currentPassword = $this->connectData->getPassword();

        $this->assertEquals($expectPassword, $currentPassword);
    }

    public function testFetchDSN()
    {
        $driver = MockConfig::MOCK_DRIVER;
        $database = MockConfig::MOCK_DATABASE;
        $host = MockConfig::MOCK_HOST;
        $expectDSN = "$driver:dbname=$database;host=$host";

        $currentDSN = $this->connectData->getDSN();

        $this->assertEquals($expectDSN, $currentDSN);
    }
}