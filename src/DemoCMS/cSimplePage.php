<?php
namespace DemoCMS;

use Inspirio\Database\aDataObject;

class cSimplePage extends aDataObject
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

		'titleImageID' => array(
			'dbColumn' => 'title_image_ID',
			'type' => 'int',
			'sanitize' => 'foreingKey',
		),

		'perex' => array(
			'dbColumn' => 'perex',
			'type'     => 'string',
		),

		'content' => array(
			'dbColumn' => 'content',
			'type'     => 'string',
		),

		'createdOn' => array(
			'dbColumn' => 'created_on',
			'type'     => 'datetime',
		),

		'editedOn' => array(
			'dbColumn' => 'edited_on',
			'type'     => 'datetime',
		),


		'webPage' => array(
			'type' => 'cWebPage'
		),

		'titleImage' => array(
			'type' => 'cImage'
		)
	);

	protected static $meta = array(
		'tableName' => 'simple_pages'
	);
}
