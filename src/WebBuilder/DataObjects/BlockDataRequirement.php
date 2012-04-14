<?php
namespace WebBuilder\DataObjects;

use Inspirio\Database\aDataObject;

class BlockDataRequirement extends aDataObject
{	
	/**
	 * DataObject properties configuration
	 *
	 * @var array
	 */
	protected static $items = array(
		'ID' => array(
			'dbColumn' => 'ID',
			'type' => 'integer',
		),

		'blockID' => array(
			'dbColumn' => 'block_ID',
			'type' => 'integer',
			'sanitize' => 'foreingKey',
		),

		'property' => array(
			'dbColumn' => 'property',
			'type' => 'string',
		),

		'dataType' => array(
			'dbColumn' => 'data_type',
			'type' => 'string',
		),

		'dataProviders' => array(
			'type' => 'array[ BlockDataRequirementProvider ]',
		),
	);
	
	/**
	 * DataObject meta info
	 *
	 * @var array
	 */
	protected static $meta = array(
			'tableName' => 'blocks_data_requirements',
	);
}