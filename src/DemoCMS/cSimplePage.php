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
			'type'     => 'int'
		),

		'titleImageID' => array(
			'dbColumn' => 'title_image_ID',
			'type' => 'int'
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
