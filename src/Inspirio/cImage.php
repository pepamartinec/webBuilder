<?php
namespace Inspirio;

use Inspirio\Database\aDataObject;

class cImage extends aDataObject
{
	protected static $items = array(
		'ID' => array(
			'dbColumn' => 'ID',
			'type'     => 'int',
		),

		'webPageID' => array(
			'dbColumn' => 'web_page_ID',
			'type'     => 'int'
		),

		'title' => array(
			'dbColumn' => 'title',
			'type'     => 'string',
		),

		'filename' => array(
			'dbColumn' => 'filename',
			'type'     => 'string',
		),

		'createdOn' => array(
			'dbColumn' => 'created_on',
			'type'     => 'datetime',
		),

		'createdBy' => array(
			'dbColumn' => 'created_by',
			'type'     => 'int',
			'sanitize' => 'foreingKey',
		),

		'editedOn' => array(
			'dbColumn' => 'edited_on',
			'type'     => 'datetime',
		),

		'editedBy' => array(
			'dbColumn' => 'edited_by',
			'type'     => 'int',
			'sanitize' => 'foreingKey',
		),
	);

	protected static $meta = array(
		'tableName' => 'images'
	);
}
