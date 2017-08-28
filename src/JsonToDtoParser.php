<?php

namespace JsonToDto;

use ReflectionClass;
use ReflectionMethod;

class JsonToDtoParser
{
	/**
	 * @param string $className
	 * @param array $json
	 *
	 * @return object
	 *
	 * @throws MissingParameterException
	 */
	public function parseToObject(string $className, array $json)
	{
		$reflectionMethod = new ReflectionMethod($className, '__construct');
		$this->checkParameters($reflectionMethod, array_keys($json));

		$parametersList = [];
		foreach ($reflectionMethod->getParameters() as $parameter) {
			$parametersList[$parameter->getPosition()] = [
				'name' => $parameter->getName(),
				'type' => $parameter->getType(),
			];
		}

		$variables = [];
		foreach ($parametersList as $parameter) {
			if (array_key_exists($parameter['name'], $json) === false) {
				continue;
			}

			if ($parameter['type']->isBuiltin()) {
				$variables[] = $json[$parameter['name']];
			} else {
				$variables[] = $this->parseToObject(
					$parameter['type']->__toString(),
					$json[$parameter['name']]
				);
			}
		}

		$reflectionClass = new ReflectionClass($className);
		return $reflectionClass->newInstanceArgs($variables);
	}

	/**
	 * @param ReflectionMethod $reflectionMethod
	 * @param array $keys
	 *
	 * @return void
	 *
	 * @throws MissingParameterException
	 */
	private function checkParameters(
		ReflectionMethod $reflectionMethod,
		array $keys
	)
	{
		foreach ($reflectionMethod->getParameters() as $parameter) {
			if ($parameter->isOptional() === false && in_array($parameter->getName(), $keys) === false) {
				throw new MissingParameterException($parameter->getName());
			}
		}
	}
}
