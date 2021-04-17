<?php


namespace NovaVision\ActiveRecord;

use CaseConverter\CaseString;
use NovaVision\Core\BaseObject;
use NovaVision\Database\Connect;
use NovaVision\Utility\FactoryConnect;
use morphos\English\NounPluralization;
use NovaVision\Configurations\EnvConfig;
use NovaVision\ActiveRecord\QueryBuilder;
use NovaVision\ActiveRecord\Exceptions\GuardException;

/**
 * Class Model
 * @package NovaVision\ActiveRecord
 * @author Artem Tyutnev <strategico.dev@gmail.com>
 */
abstract class Model extends BaseObject
{
    /**
     * @var string
     */
    protected string $table = '';

    /**
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * An original model data
     *
     * @var array
     */
    protected array $original = [];

    /**
     * Properties for work with save, update methods
     *
     * @var array
     */
    protected array $fillable = [];

    /**
     * @var array
     */
    protected array $hidden = [];

    /**
     * @var array
     */
    protected array $guarded = [];

    /**
     * @var array
     */
    private array $properties = [];

    /**
     * This array contains properties that are defined as hidden
     * @var array
     */
    private array $protectedProperties = [];

    /**
     * @var bool
     */
    private bool $exists = false;

    /**
     * Model constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->load($attributes);
    }

    /**
     * @param string $property
     * @return bool
     */
    private function isHidden(string $property)
    {
        return in_array($property, $this->hidden);
    }

    /**
     * @param string $property
     * @return bool
     */
    private function isGuarded(string $property)
    {
        return in_array($property, $this->guarded);
    }

    /**
     * @return bool
     */
    private function recordAlreadyExists()
    {
        return $this->exists;
    }

    /**
     * @param string $property
     * @param $value
     */
    private function setProtectedProperty(string $property, $value)
    {
        $this->protectedProperties[$property] = $value;
    }

    private function getCustomGetter(string $property)
    {
        $property = CaseString::snake($property)->pascal();
        return 'get' . $property . 'Property';
    }

    private function hasCustomGetter(string $property)
    {
        $getter = $this->getCustomGetter($property);
        return method_exists($this, $getter);
    }

    private function getCustomSetter(string $property)
    {
        $property = CaseString::snake($property)->pascal();
        return 'set' . $property . 'Property';
    }

    private function hasCustomSetter(string $property)
    {
        $setter = $this->getCustomGetter($property);
        return method_exists($this, $setter);
    }

    /**
     * @return array
     */
    private function getChangedProperties()
    {
        return array_diff($this->properties, $this->original);
    }

    /**
     * @return bool
     */
    private function hasChanged()
    {
        return !!count($this->getChangedProperties());
    }

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if($this->isHidden($property))
        {
            return $this->protectedProperties[$property];
        }
        if($this->hasCustomGetter($property))
        {
            $getter = $this->getCustomGetter($property);
            return $this->$getter($this->properties[$property]);
        }

        return $this->properties[$property] ?? null;
    }

    /**
     * @param $property
     * @param $value
     * @throws GuardException
     */
    public function __set($property, $value)
    {
        if(!isset($this->original[$property]))
        {
            $this->original[$property] = $value;
        }

        if($this->isGuarded($property))
        {
            throw new GuardException($property);
        }
        if($this->hasCustomSetter($property))
        {
            $setter = $this->getCustomSetter($property);
            $this->$setter($value);
        }
        if($this->isHidden($property))
        {
            $this->setProtectedProperty($property, $value);
        }

        $this->properties[$property] = $value;
    }

    /**
     * @param array $attributes
     */
    public function load(array $attributes)
    {
        foreach($attributes as $property => $value)
        {
            $this->$property = $value;
        }
    }

    public function getTable()
    {
        if(!$this->table)
        {
            $this->table = NounPluralization::pluralize($this->getClassName());
        }

        return $this->table;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param string $primaryKey
     */
    public function setPrimaryKey(string $primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * @return mixed
     */
    public function getPrimaryValue()
    {
        return $this->{$this->getPrimaryKey()};
    }

    public function setPrimaryValue($value)
    {
        $this->{$this->getPrimaryKey()} = $value;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if($this->recordAlreadyExists())
        {
            return self::query()->
                         update($this->getChangedProperties())->
                         where($this->getPrimaryKey(), '=', $this->getPrimaryValue())->
                         execute();
        }

        $this->setPrimaryValue(self::query()->insert($this->properties));
        $this->exists = $this->getPrimaryValue() !== false;

        return $this->exists;
    }

    /**
     * @return \NovaVision\ActiveRecord\QueryBuilder
     */
    public static function query()
    {
        $modelInstance = new static;
    
        return new QueryBuilder(
            FactoryConnect::factory(EnvConfig::class, Connect::class),
            $modelInstance->getTable(),
            static::class
        );
    }
}