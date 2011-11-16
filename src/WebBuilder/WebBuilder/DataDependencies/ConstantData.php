<?php
namespace WebBuilder\WebBuilder\DataDependencies;

use WebBuilder\WebBuilder\DataDependencyInterface;
use WebBuilder\WebBuilder\BlockInstance;

class ConstantData implements DataDependencyInterface
{
	/**
	 * @var string
	 */
	protected $property;
	
	/**
	 * @var mixed
	 */
	protected $value;
	
	/**
	 * Constructor
	 *
	 * @param string $property
	 * @param mixed  $value
	 */
	public function __construct( $property, $value )
	{
		$this->property = $property;
		$this->value    = $value;
	}
	
	/**
	 * Returns target block property name
	 *
	 * @return string
	 */
	public function getProperty()
	{
		return $this->property;
	}
	
	/**
	 * Returns data provider
	 *
	 * @return \WebBuilder\WebBuilder\BlockInstance|null
	 */
	public function getProvider()
	{
		return null;
	}
	
	/**
	 * Returns dependency target data
	 *
	 * @return mixed
	 *
	 * @throws DataIntegrityException
	 */
	public function getTargetData()
	{
		return $this->value;
	}
}