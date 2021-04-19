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
    private array $attributes = [];

    /**
     * This array contains properties that are defined as hidden
     * @var array
     */
    private array $protectedAttributes = [];

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
     * @param string $attribute
     * @return bool
     */
    private function isHidden(string $attribute)
    {
        return in_array($attribute, $this->hidden);
    }

    /**
     * @param string $attribute
     * @return bool
     */
    private function isGuarded(string $attribute)
    {
        return in_array($attribute, $this->guarded);
    }

    /**
     * @return bool
     */
    private function recordAlreadyExists()
    {
        return $this->exists;
    }

    /**
     * @param string $attribute
     * @param $value
     */
    private function setProtectedProperty(string $attribute, $value)
    {
        $this->protectedAttributes[$attribute] = $value;
    }

    private function getCustomGetter(string $attribute)
    {
        $attribute = CaseString::snake($attribute)->pascal();
        return 'get' . $attribute . 'Attribute';
    }

    private function hasCustomGetter(string $attribute)
    {
        $getter = $this->getCustomGetter($attribute);
        return method_exists($this, $getter);
    }

    private function getCustomSetter(string $attribute)
    {
        $attribute = CaseString::snake($attribute)->pascal();
        return 'set' . $attribute . 'Attribute';
    }

    private function hasCustomSetter(string $attribute)
    {
        $setter = $this->getCustomGetter($attribute);
        return method_exists($this, $setter);
    }

    /**
     * @return array
     */
    private function getChangedAttributes()
    {
        return array_diff($this->attributes, $this->original);
    }

    /**
     * @return bool
     */
    private function hasChanged()
    {
        return !!count($this->getChangedAttributes());
    }

    /**
     * @param $attribute
     * @return mixed|null
     */
    public function getAttribute($attribute)
    {
        if($this->isHidden($attribute))
        {
            return $this->protectedAttributes[$attribute];
        }
        if($this->hasCustomGetter($attribute))
        {
            $getter = $this->getCustomGetter($attribute);
            return $this->$getter($this->attributes[$attribute]);
        }

        return $this->properties[$attribute] ?? null;
    }

    /**
     * @param $attribute
     * @param $value
     * @throws GuardException
     */
    public function setAttribute($attribute, $value)
    {
        if(!isset($this->original[$attribute]))
        {
            $this->original[$attribute] = $value;
        }

        if($this->isGuarded($attribute))
        {
            throw new GuardException($attribute);
        }
        if($this->hasCustomSetter($attribute))
        {
            $setter = $this->getCustomSetter($attribute);
            $this->$setter($value);
        }
        if($this->isHidden($attribute))
        {
            $this->setProtectedProperty($attribute, $value);
        }

        $this->attributes[$attribute] = $value;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        return $this->getAttribute($attribute);
    }

    /**
     * @param $attribute
     * @param $value
     * @throws GuardException
     */
    public function __set($attribute, $value)
    {
        $this->setAttribute($attribute, $value);
    }

    /**
     * @param array $attributes
     * @throws GuardException
     */
    public function load(array $attributes)
    {
        foreach($attributes as $attribute => $value)
        {
            $this->setAttribute($attribute, $value);
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
            return $this->updateMyself();
        }

        $primaryKey = self::query()->insert($this->getAttributes());
        $this->setPrimaryValue($primaryKey);
        $this->exists = is_int($primaryKey);

        return $this->exists;
    }

    /**
     * @return bool
     */
    public function updateMyself()
    {
        return self::query()->
                     update($this->getChangedAttributes())->
                     where($this->getPrimaryKey(), '=', $this->getPrimaryValue())->
                     execute();
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