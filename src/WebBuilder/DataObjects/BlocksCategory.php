<?php
namespace WebBuilder\DataObjects;

use Inspirio\Database\aDataObject;

class BlocksCategory extends aDataObject
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

		'title' => array(
			'dbColumn' => 'title',
			'type'     => 'string',
		),

		'blocks' => array(
			'type' => 'array[ Block ]',
		),
	);

	/**
	 * DataObject meta info
	 *
	 * @var array
	 */
	protected static $meta = array(
		'tableName' => 'blocks_categories',
	);
}