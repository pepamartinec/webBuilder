<?php
namespace WebBuilder\DataObjects;

use Inspirio\Database\aDataObject;

class BlockDataRequirementProvider extends aDataObject
{	
	/**
	 * DataObject properties configuration
	 *
	 * @var array
	 */
	protected static $items = array(
		'requiredPropertyID' => array(
			'dbColumn' => 'required_property_ID',
			'type' => 'integer',
			'sanitize' => 'foreingKey',
		),

		'providerID' => array(
			'dbColumn' => 'provider_ID',
			'type' => 'integer',
			'sanitize' => 'foreingKey',
		),

		'providerProperty' => array(
			'dbColumn' => 'provider_property',
			'type' => 'string',
		),
	);
	
	/**
	 * DataObject meta info
	 *
	 * @var array
	 */
	protected static $meta = array(
			'tableName' => 'blocks_data_requirements_providers',
	);
}