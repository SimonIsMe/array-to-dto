<?php

namespace JsonToDto;

class ValidationException extends \Exception
{
	/**
	 * @var string
	 */
	private $errors;

	/**
	 * @param string $errors
	 */
	public function __construct($errors)
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
