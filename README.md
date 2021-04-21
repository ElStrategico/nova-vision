# NovaVision
Active record pattern is an approach to accessing data in a database. A database table or view is wrapped into a class.
See more: https://en.wikipedia.org/wiki/Active_record_pattern

### Using
Define in .env file a configurations to database
Define a class that will be a table in the database:
```php
use NovaVision\ActiveRecord\Model;

class User extends Model
{}
```
This class will be work with table users
You can override table name:
```php
use NovaVision\ActiveRecord\Model;

class User extends Model
{
	protected $table = 'table';
}
```
### Query to database
Query for getting all data from table:
```php
User::query()->get();
```
For getting a single row from table:
```php
User::query()->first();
```
Getting with conditions:
```php
User::query()->where('id', '=', 1)->first();
```
```php
use NovaVision\ActiveRecord\Aliases;

User::query()->where('name', '=', 'Foo')->orderBy('id', Aliases::DESC_SORT)->limit(10)->get();
```
Joins:
```php
Post::query()->innerJoin('users', 'user_id', '=', 'id')->get();
```
### Model properties
Fillable - define attributes for work with save, update methods. Attributes defined in this array can be saved and updated.
```php
/**
 * @property int $id
 */
class User extends Model
{
    protected array $fillable = [
       'id',  'email', 'first_name', 'last_name', 'password'
    ];
}
```
Hidden attributes not visible when serializing an object.
```php
/**
 * @property int $id
 */
class User extends Model
{
    protected array $hidden = [
        'password'
    ];
}
```
Guarded attributes can not rewrite
```php
/**
 * @property int $id
 */
class User extends Model
{
    protected array $guarded = [
        'password'
    ];
}
```
### Custom getters and setters
You can define custom getter:
```php
class User extends Model
{
    public function getFullNameAttribute()
    {
        return "$this->first_name $this->last_name";
    }
}
```
Using:
```php
$user = User::query()->first();
$user->fullName
```
You can define custom setter:
```php
class User extends Model
{
    public function setFullNameAttribute($value)
    {
        $this->first_name = $value['first_name'];
        $this->last_name = $value['last_name'];
    }
}
```
Using:
```php
$user = User::query()->first();
$user->fullName = [
	'first_name' => 'Foo',
	'last_name' => 'Bar'
];
```