<?php

use JsonToDto\JsonToDtoParser;
use JsonToDto\MissingParameterException;
use JsonToDto\ValidationException;
use PHPUnit\Framework\TestCase;

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

class NestedUser
{
	public $value;
	public $user;

	public function __construct(int $value, User $user)
	{
		$this->value = $value;
		$this->user = $user;
	}
}

class ObjectWithArray
{
	public $array;
	public function __construct(array $array)
	{
		$this->array = $array;
	}
}


class ValidateObject
{
	public $emailAddress;
	public $positiveNumber;

	public function __construct(string $emailAddress, int $positiveNumber)
	{
		$errors = [];

		if ($positiveNumber <= 0) {
			$errors['positiveNumber'] = 'This value should be positive';
		}

		if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL) === false) {
			$errors['emailAddress'] = 'Invalid email address';
		}

		if (empty($errors) == false) {
			throw new ValidationException($errors);
		}

		$this->emailAddress = $emailAddress;
		$this->positiveNumber = $positiveNumber;
	}
}

class NestedValidateObject
{
	public $validateObjectA;
	public $validateObjectB;

	public function __construct(ValidateObject $validateObjectA, ValidateObject $validateObjectB)
	{
		$this->validateObjectA = $validateObjectA;
		$this->validateObjectB = $validateObjectB;
	}
}

class JsonToDtoParserTest extends TestCase
{
	public function test_parseToObject()
	{
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

		$this->assertEquals('string', $user->string);
		$this->assertEquals('', $user->optional);
		$this->assertEquals(false, $user->bool);
		$this->assertEquals(789, $user->int);
		$this->assertEquals(123.456, $user->float);
	}

	public function test_parseToObject_with_extra_element_in_json()
	{
		$parser = new JsonToDtoParser();
		$user = $parser->parseToObject(
			User::class,
			[
				'string' => 'string',
				'bool' => false,
				'float' => 123.456,
				'int' => 789,
				'extraElement' => 'Lorem ipsum dolorem',
			]
		);

		$this->assertEquals('string', $user->string);
		$this->assertEquals(false, $user->bool);
		$this->assertEquals(789, $user->int);
		$this->assertEquals(123.456, $user->float);
	}

	public function test_parseToObject_for_validatable_object()
	{
		$parser = new JsonToDtoParser();
		$email = $parser->parseToObject(
			ValidateObject::class,
			[
				'emailAddress' => 'address@domain.com',
				'positiveNumber' => 123
			]
		);

		$this->assertEquals('address@domain.com', $email->emailAddress);
	}

	public function test_parseToObject_for_validatable_nested_object()
	{
		$parser = new JsonToDtoParser();
		$email = $parser->parseToObject(
			NestedValidateObject::class,
			[
				'validateObjectA' => [
					'emailAddress' => 'address@domain.com',
					'positiveNumber' => 123
				],
				'validateObjectB' => [
					'emailAddress' => 'address_b@domain.com',
					'positiveNumber' => 987
				],
			]
		);

		$this->assertEquals('address@domain.com', $email->validateObjectA->emailAddress);
		$this->assertEquals(123, $email->validateObjectA->positiveNumber);
		$this->assertEquals('address_b@domain.com', $email->validateObjectB->emailAddress);
		$this->assertEquals(987, $email->validateObjectB->positiveNumber);
	}

	public function test_parseToObject_for_validatable_object_when_value_is_incorrect()
	{
		$parser = new JsonToDtoParser();

		try {
			$parser->parseToObject(
				ValidateObject::class,
				[
					'emailAddress' => 'abc',
					'positiveNumber' => -123
				]
			);
			$this->assertTrue(false);
		} catch (ValidationException $exception) {
			$this->assertEquals([
				'emailAddress' => 'Invalid email address',
				'positiveNumber' => 'This value should be positive'
			], $exception->getErrors());
		}
	}

	public function test_parseToObject_without_required_parameter()
	{
		$parser = new JsonToDtoParser();

		$this->expectException(MissingParameterException::class);
		$parser->parseToObject(User::class, []);
	}

	public function test_parseToObject_with_set_optional_parameter()
	{
		$parser = new JsonToDtoParser();
		$user = $parser->parseToObject(
			User::class,
			[
				'string' => 'string',
				'bool' => false,
				'float' => 123.456,
				'int' => 789,
				'optional' => 'optional',
			]
		);

		$this->assertEquals('string', $user->string);
		$this->assertEquals('optional', $user->optional);
		$this->assertEquals(false, $user->bool);
		$this->assertEquals(789, $user->int);
		$this->assertEquals(123.456, $user->float);
	}

	public function test_parseToObject_with_nested_objects()
	{
		$parser = new JsonToDtoParser();
		$nestedUser = $parser->parseToObject(
			NestedUser::class,
			[
				'value' => 111,
				'user' => [
					'string' => 'string',
					'bool' => false,
					'float' => 123.456,
					'int' => 789,
				]
			]
		);

		$this->assertEquals('string', $nestedUser->user->string);
		$this->assertEquals('', $nestedUser->user->optional);
		$this->assertEquals(false, $nestedUser->user->bool);
		$this->assertEquals(789, $nestedUser->user->int);
		$this->assertEquals(123.456, $nestedUser->user->float);
		$this->assertEquals(111, $nestedUser->value);
	}

	public function test_parseToObject_with_array()
	{
		$parser = new JsonToDtoParser();
		$nestedUser = $parser->parseToObject(
			ObjectWithArray::class,
			[
				'array' => [
					'a', 'b', 12, 34.56, true
				]
			]
		);

		$this->assertEquals(['a', 'b', 12, 34.56, true], $nestedUser->array);
	}

}
