<?php

namespace JsonToDto;

class ValidationException extends \Exception
{
	/**
	 * @var array
	 */
	private $errors;

	/**
	 * @param array $errors
	 */
	public function __construct(array $errors)
	{
		$this->errors = $errors;
		parent::__construct();
	}

	/**
	 * @return array
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}

}
