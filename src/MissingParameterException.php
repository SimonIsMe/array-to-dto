<?php

namespace JsonToDto;

class MissingParameterException extends \Exception
{
	/**
	 * @var string
	 */
	private $parameterName;

	/**
	 * @param string $parameterName
	 */
	public function __construct($parameterName)
	{
		$this->parameterName = $parameterName;
	}

	/**
	 * @return string
	 */
	public function getParameterName(): string
	{
		return $this->parameterName;
	}

}
