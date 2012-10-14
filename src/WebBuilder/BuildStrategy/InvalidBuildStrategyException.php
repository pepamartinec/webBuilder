<?php
namespace WebBuilder\BuildStrategy;

class InvalidBuildStrategyException extends \DomainException
{
	/**
	 * Constructor
	 *
	 * @param string $type supplied build strategy name
	 */
	public function __construct($name)
	{
		parent::__construct("Invalid build strategy '{$name}' supplied");
	}
}
