<?php


namespace ActiveRecord;

use PHPUnit\Framework\TestCase;
use NovaVision\Database\IConnect;
use NovaVision\ActiveRecord\QueryBuilder;

class MockConnect implements IConnect
{
    public function __construct(string $dsn = '', string $user = '', string $password = '')
    {

    }

    public function execute(string $sql = '', array $prepareParams = []): bool
    {
        return true;
    }

    public function insert(string $sql = '', array $prepareParams = [])
    {}

    public function fetch(string $sql = '', array $prepareParams = [])
    {
        return [
            ['id' => 1, 'title' => 'Foo', 'description' => 'Hello, World!'],
            ['id' => 2, 'title' => 'Bar', 'description' => 'Hello, World!'],
            ['id' => 3, 'title' => 'Zoo', 'description' => 'Hello, World!']
        ];
    }

    public function fetchOne(string $sql = '', array $prepareParams = [])
    {
        return $this->fetch($sql, $prepareParams)[0];
    }

    public function scalarFetch(string $sql = '', array $prepareParams = [])
    {
        return 1;
    }
}

class QueryBuilderTest extends TestCase
{
    /**
     * @var MockConnect
     */
    private MockConnect $mockConnect;

    /**
     * @var QueryBuilder
     */
    private QueryBuilder $queryBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockConnect = new MockConnect();
        $this->queryBuilder = new QueryBuilder(
            $this->mockConnect,
            'table'
        );
    }

    public function testGet()
    {
        $expectedData = $this->mockConnect->fetch();

        $data = $this->queryBuilder->get();

        $this->assertIsArray($data);
        $this->assertSameSize($expectedData, $data);
        $this->assertEquals($expectedData, $data);
    }

    public function testFirst()
    {
        $expectedData = $this->mockConnect->fetchOne();

        $data = $this->queryBuilder->first();

        $this->assertNotNull($data);
        $this->assertEquals($expectedData, $data);
    }
}