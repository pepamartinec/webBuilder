<?php
namespace WebBuilder\DataDependency;

use WebBuilder\BlockInstance;

use WebBuilder\DataIntegrityException;
use WebBuilder\DataDependencyInterface;

class UndefinedData implements DataDependencyInterface
{
	/**
	 * Dependent block instance
	 *
	 * @var BlockInstance
	 */
	protected $instance;

	/**
	 * ID of BlockDataRequirement instance
	 *
	 * @var integer
	 */
	protected $propertyID;

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
	 * @param int           $propertyID
	 * @param string        $property
	 */
	public function __construct( BlockInstance $instance, $propertyID, $property )
	{
		$this->instance   = $instance;
		$this->propertyID = $propertyID;
		$this->property   = $property;
	}

	/**
	 * Returns ID of BlockDataRequirement
	 *
	 * @return int
	 */
	public function getPropertyID()
	{
		return $this->propertyID;
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
	 * @return \WebBuilder\BlockInstance|null
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

	/**
	 * Exports internal data for client-side usage
	 *
	 * @return mixed
	 */
	public function export()
	{
		return null;
	}
}