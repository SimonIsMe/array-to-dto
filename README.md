# json-to-dto

This is simple library to convert array (parsed from json) to specified 
data transfer objects.

## Example

```php

class User
{
	public $string;
	public $int;
	public $bool;
	public $float;
	public $optional;

	public function __construct(string $string, int $int, bool $bool, float $float, string $optional = '')
	{
		$this->string = $string;
		$this->int = $int;
		$this->bool = $bool;
		$this->float = $float;
		$this->optional = $optional;
	}
}

$parser = new JsonToDtoParser();
$user = $parser->parseToObject(
    User::class,
    [
        'string' => 'string',
        'bool' => false,
        'float' => 123.456,
        'int' => 789,
    ]
);

//  $user variable contains correct User object
```

For more examples please look into /tests directory.

## Important

Keys in the array and constructor's parameters have to have the same names!
