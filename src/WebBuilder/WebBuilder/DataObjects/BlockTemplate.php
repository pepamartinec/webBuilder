<?php
namespace WebBuilder\WebBuilder\DataObjects;

use Inspirio\Database\aDataObject;

class BlockTemplate extends aDataObject
{	
	/**
	 * DataObject properties configuration
	 *
	 * @var array
	 */
	protected static $items = array(
		'ID' => array(
			'dbColumn' => 'ID',
			'type'     => 'integer',
		),

		'blockID' => array(
			'dbColumn' => 'block_ID',
			'type'     => 'integer',
			'sanitize' => 'foreingKey',
		),

		'filepath' => array(
			'dbColumn' => 'filepath',
			'type'     => 'string',
		),

		'filename' => array(
			'dbColumn' => 'filename',
			'type'     => 'string',
		),

		'slots' => array(
			'type' => 'array[ BlockTemplateSlot ]',
		),

		'block' => array(
			'type' => 'dBlock',
		),
	);
	
	/**
	 * DataObject meta info
	 *
	 * @var array
	 */
	protected static $meta = array(
		'tableName' => 'blocks_templates',
	);
}