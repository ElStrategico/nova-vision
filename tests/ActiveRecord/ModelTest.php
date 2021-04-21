<?php

use PHPUnit\Framework\TestCase;
use NovaVision\ActiveRecord\Model;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $fullName
 */
class User extends Model
{
    protected array $fillable = [
        'id', 'first_name', 'last_name'
    ];

    public function getFullNameAttribute()
    {
        return "$this->first_name $this->last_name";
    }

    public function setFullNameAttribute($value)
    {
        $this->first_name = $value['first_name'];
        $this->last_name = $value['last_name'];
    }
}

class ModelTest extends TestCase
{
    /**
     * @var User
     */
    private User $user;

    /**
     * @var array
     */
    private array $userData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->userData = [
            'id'         => 1,
            'first_name' => 'Foo',
            'last_name'  => 'Bar'
        ];
    }

    public function testTableName()
    {
        $expectedTable = 'users';

        $table = $this->user->getTable();

        $this->assertEquals($expectedTable, $table);
    }

    public function testLoad()
    {
        $this->user->load($this->userData);

        $this->assertEquals($this->userData['id'], $this->user->id);
        $this->assertEquals($this->userData['first_name'], $this->user->first_name);
        $this->assertEquals($this->userData['last_name'], $this->user->last_name);
    }

    public function testCustomGetter()
    {
        $firstName = $this->userData['first_name'];
        $lastName = $this->userData['last_name'];
        $expectedFullName = "$firstName $lastName";

        $this->user->load($this->userData);

        $this->assertNotNull($this->user->fullName);
        $this->assertEquals($expectedFullName, $this->user->fullName);
    }

    public function testCustomSetter()
    {
        $firstName = 'John';
        $lastName = 'Doe';
        $expectedFullName = "$firstName $lastName";

        $this->user->fullName = [
            'first_name' => $firstName,
            'last_name'  => $lastName
        ];

        $this->assertNotNull($this->user->fullName);
        $this->assertEquals($expectedFullName, $this->user->fullName);
    }
}