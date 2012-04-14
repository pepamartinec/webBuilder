<?php
namespace WebBuilder\DataObjects;

use Inspirio\Database\aDataObject;
use WebBuilder\WebPageInterface;

class WebPage extends aDataObject implements WebPageInterface
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

		'parentID' => array(
			'dbColumn' => 'parent_ID',
			'type' => 'integer',
			'sanitize' => 'foreingKey',
		),

		'blocksSetID' => array(
			'dbColumn' => 'blocks_set_ID',
			'type' => 'integer',
			'sanitize' => 'foreingKey',
		),

		'entityType' => array(
			'dbColumn' => 'entity_type',
			'type' => 'string',
		),

		'entityID' => array(
			'dbColumn' => 'entity_ID',
			'type' => 'integer',
			'sanitize' => 'foreingKey',
		),

		'title' => array(
			'dbColumn' => 'title',
			'type' => 'string',
		),

		'urlName' => array(
			'dbColumn' => 'url_name',
			'type' => 'string',
		),

		'createdOn' => array(
			'dbColumn' => 'created_on',
			'type' => 'string',
		),

		'createdBy' => array(
			'dbColumn' => 'created_by',
			'type' => 'integer',
		),

		'editedOn' => array(
			'dbColumn' => 'edited_on',
			'type' => 'string',
		),

		'editedBy' => array(
			'dbColumn' => 'edited_by',
			'type' => 'integer',
		)
	);

	/**
	 * DataObject meta info
	 *
	 * @var array
	 */
	protected static $meta = array(
		'tableName' => 'web_pages',
	);

	public function __toString()
	{
		return $this->get( 'title' );
	}
}