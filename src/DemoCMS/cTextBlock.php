<?php
namespace DemoCMS;

use Inspirio\Database\aDataObject;

class cTextBlock extends aDataObject
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
			'type' => 'string',
		),

		'imageID' => array(
			'dbColumn' => 'image_ID',
			'type' => 'int',
			'sanitize' => 'foreingKey'
		),

		'content' => array(
			'dbColumn' => 'content',
			'type' => 'string',
		),

		'createdOn' => array(
			'dbColumn' => 'created_on',
			'type' => 'string',
		),

		'editedOn' => array(
			'dbColumn' => 'edited_on',
			'type' => 'string',
		),
	);

	/**
	 * DataObject meta info
	 *
	 * @var array
	 */
	protected static $meta = array(
			'tableName' => 'text_blocks',
	);

	public function __toString()
	{
		return $this->get( 'title' );
	}
}
