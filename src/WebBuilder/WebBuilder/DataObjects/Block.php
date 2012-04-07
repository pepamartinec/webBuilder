<?php
namespace WebBuilder\WebBuilder\DataObjects;

use Inspirio\Database\aDataObject;

class Block extends aDataObject
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

		'categoryID' => array(
			'dbColumn' => 'category_ID',
			'type' => 'integer',
		),

		'codeName' => array(
			'dbColumn' => 'code_name',
			'type' => 'string',
		),

		'title' => array(
			'dbColumn' => 'title',
			'type' => 'string',
		),

		'templates' => array(
			'type' => 'array[ BlockTemplate ]',
		),

		'dataRequirements' => array(
			'type' => 'array[ BlockDataRequirement ]',
		),
	);

	/**
	 * DataObject meta info
	 *
	 * @var array
	 */
	protected static $meta = array(
		'tableName' => 'blocks',
	);
}