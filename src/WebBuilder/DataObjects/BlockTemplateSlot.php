<?php
namespace WebBuilder\DataObjects;

use Inspirio\Database\aDataObject;

class BlockTemplateSlot extends aDataObject
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

		'templateID' => array(
			'dbColumn' => 'template_ID',
			'type' => 'integer',
			'sanitize' => 'foreingKey',
		),

		'codeName' => array(
			'dbColumn' => 'code_name',
			'type' => 'string',
		),

		'title' => array(
			'dbColumn' => 'title',
			'type'     => 'string',
		),

		'template' => array(
			'type' => 'dBlockTemplate',
		),
	);

	/**
	 * DataObject meta info
	 *
	 * @var array
	 */
	protected static $meta = array(
			'tableName' => 'blocks_templates_slots',
	);
}