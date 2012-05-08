<?php
namespace DemoCMS;

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
			'type'     => 'int',
			'sanitize' => 'foreingKey',
		),

		'title' => array(
			'dbColumn' => 'title',
			'type'     => 'string',
		),

		'filenameFull' => array(
			'dbColumn' => 'filename_full',
			'type'     => 'string',
		),

		'filenameThumb' => array(
			'dbColumn' => 'filename_thumb',
			'type'     => 'string',
		),

		'createdOn' => array(
			'dbColumn' => 'created_on',
			'type'     => 'datetime',
		),
	);

	protected static $meta = array(
		'tableName' => 'images'
	);
}
