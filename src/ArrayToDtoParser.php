<?php

namespace JsonToDto;

use ReflectionClass;
use ReflectionMethod;

class ArrayToDtoParser
{
	public function parseToDto(string $dtoClass, $data)
	{
		$reflectionMethod = new ReflectionMethod($dtoClass, '__construct');

		if (is_array($data) === false) {
			$reflectionClass = new ReflectionClass($dtoClass);
			return $reflectionClass->newInstanceArgs([ $data ]);
		}

		$variables = [];
		foreach ($reflectionMethod->getParameters() as $parameter) {
			if (array_key_exists($parameter->getName(), $data) === false) {
				throw new MissingParameterException($parameter->getName());
			}

			if ($parameter->getType()->isBuiltin()) {
				$variables[] = $data[$parameter->getName()];
				continue;
			}

			$variables[] = $this->parseToDto(
				$parameter->getType(),
				$data[$parameter->getName()]
			);
		}

		$reflectionClass = new ReflectionClass($dtoClass);
		return $reflectionClass->newInstanceArgs($variables);
	}
}
