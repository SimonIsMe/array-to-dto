<?php

use JsonToDto\ArrayToDtoParser;
use JsonToDto\MissingParameterException;
use PHPUnit\Framework\TestCase;

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

class ArrayToDtoParserTest extends TestCase
{
	public function test_parseToDto_with_incorrect_parameters()
	{
		$arrayToDtoParse = new ArrayToDtoParser();
		$this->expectException(MissingParameterException::class);
		$arrayToDtoParse->parseToDto(Dto::class, ['a' => 'a']);
	}

	/**
	 * @dataProvider dataProvider_parseToDto
	 */
	public function test_parseToDto($dtoClass, $data, $expectedResult)
	{
		$arrayToDtoParse = new ArrayToDtoParser();
		$result  = $arrayToDtoParse->parseToDto($dtoClass, $data);

		$this->assertEquals($expectedResult, $result);
	}

	public function dataProvider_parseToDto()
	{
		return [
			[
				EmailAddress::class,
				'address@domain.com',
				new EmailAddress('address@domain.com')
			],
			[
				EmailAddress::class,
				[
					'email' => 'address@domain.com'
				],
				new EmailAddress('address@domain.com')
			],
			[
				User::class,
				[
					'emailAddress' => 'address@domain.com',
					'name' => 'Simon',
					'birthDate' => '1991-01-02',
				],
				new User(
					new EmailAddress('address@domain.com'),
					'Simon',
					new DateTime('1991-01-02')
				),
			],
			[
				Dto::class,
				[
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
				],
				new Dto(
					new User(
						new EmailAddress('address@domain.com'),
						'Simon',
						new DateTime('1991-01-02')
					),
					new Nested(
						new User(
							new EmailAddress('address2@domain.com'),
							'Simon',
							new DateTime('1991-01-02')
						),
						new EmailAddress('address3@domain.com')
					)
				)
			],
		];
	}
}
