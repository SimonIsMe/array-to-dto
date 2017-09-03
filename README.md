# json-to-dto

This is simple library to convert array to specified value object classes.

## Example


### Simple object

```php


class EmailAddress
{
	public $email;

	public function __construct(string $email)
	{
		$this->email = $email;
	}

}

$arrayToDtoParse = new ArrayToDtoParser();
$result  = $arrayToDtoParse->parseToDto(EmailAddress::class, [
    'address@domain.com';
]);

//  $result variable contains correct EmailAddress object
echo $result->email; // address@domain.com
```

### Nested object

```php

class User
{
	public $emailAddress;
	public $number;
	public $birthDate;

	public function __construct(EmailAddress $emailAddress, string $name, \DateTime $birthDate)
	{
		$this->emailAddress = $emailAddress;
		$this->number = $name;
		$this->birthDate = $birthDate;
	}
}

class Nested
{
	public $user;
	public $emailAddress;

	public function __construct(User $user, EmailAddress $emailAddress)
	{
		$this->user = $user;
		$this->emailAddress = $emailAddress;
	}
}

class EmailAddress
{
	public $email;

	public function __construct(string $email)
	{
		$this->email = $email;
	}
}

class Dto
{
	public $user;
	public $nested;

	public function __construct(User $user, Nested $nested)
	{
		$this->user = $user;
		$this->nested = $nested;
	}
}

$arrayToDtoParse = new ArrayToDtoParser();
$result  = $arrayToDtoParse->parseToDto(Dto::class, [
    'user' => [
        'emailAddress' => 'address@domain.com',
        'name' => 'Simon',
        'birthDate' => '1991-01-02',
    ],
    'nested' => [
        'user' => [
            'emailAddress' => 'address2@domain.com',
            'name' => 'Simon',
            'birthDate' => '1991-01-02',
        ],
        'emailAddress' => 'address3@domain.com'
    ]
]);

```


For more examples please look into /tests directory.

## Important

Keys in the array and constructor's parameters have to have the same names!
