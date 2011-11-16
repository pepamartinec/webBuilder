<?php
namespace WebBuilder\WebBuilder\DataDependencies;

use WebBuilder\WebBuilder\BlockInstance;

use WebBuilder\WebBuilder\DataIntegrityException;
use WebBuilder\WebBuilder\DataDependencyInterface;

class UndefinedData implements DataDependencyInterface
{
	/**
	 * Dependent block instance
	 *
	 * @var BlockInstance
	 */
	protected $instance;
	
	/**
	 * Dependent property
	 *
	 * @var string
	 */
	protected $property;
	
	/**
	 * Constructor
	 *
	 * @param BlockInstance $instance
	 * @param string         $property
	 */
	public function __construct( BlockInstance $instance, $property )
	{
		$this->instance = $instance;
		$this->property = $property;
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
		throw new DataIntegrityException( "Unresolvable property '{$this->property}' in block '{$this->instance}'" );
	}
}