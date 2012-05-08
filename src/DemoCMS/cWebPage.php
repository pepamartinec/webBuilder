<?php
namespace DemoCMS;

use Inspirio\Database\aDataObject;
use WebBuilder\WebPageInterface;

class cWebPage extends aDataObject implements WebPageInterface
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

		'position' => array(
			'dbColumn' => 'position',
			'type' => 'integer',
		),

		'blockSetID' => array(
			'dbColumn' => 'block_set_ID',
			'type' => 'integer',
			'sanitize' => 'foreingKey',
		),

		'type' => array(
			'dbColumn' => 'type',
			'type' => 'string',
		),

		'title' => array(
			'dbColumn' => 'title',
			'type' => 'string',
		),

		'urlName' => array(
			'dbColumn' => 'url_name',
			'type' => 'string',
		),

		'published' => array(
			'dbColumn' => 'published',
			'type' => 'boolean',
		),

		'validFrom' => array(
			'dbColumn' => 'valid_from',
			'type' => 'datetime',
			'sanitize' => 'nullOnEmpty',
		),

		'validTo' => array(
			'dbColumn' => 'valid_to',
			'type' => 'datetime',
			'sanitize' => 'nullOnEmpty',
		),

		'createdOn' => array(
			'dbColumn' => 'created_on',
			'type' => 'string',
		),

		'editedOn' => array(
			'dbColumn' => 'edited_on',
			'type' => 'string',
		),



		'children' => array(
			'type' => 'array[ cWebPage ]'
		),

		'contentItem' => array(
			'type' => 'mixed'
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

	public function getBlockSetID()
	{
		return $this->get( 'blockSetID' );
	}

	public function getTitle()
	{
		return $this->get( 'title' );
	}
}