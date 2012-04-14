<?php
namespace WebBuilder\DataObjects;

use Inspirio\Database\aDataObject;

class BlockSet extends aDataObject
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
	
		'name' => array(
			'dbColumn' => 'name',
			'type' => 'string',
		),
		
		'parentID' => array(
			'dbColumn' => 'parent_ID',
			'type' => 'integer',
			'sanitize' => 'foreingKey',
		),
	
		'pregeneratedStructure' => array(
			'dbColumn' => 'pregenerated_structure',
			'type' => 'string',
		),
	
		'builderType' => array(
			'dbColumn' => 'builder_type',
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
		),
	
		'blocks' => array(
			'type' => 'array[ \WebBuilder\DataObjects\Block ]',
		),
	);
	
	/**
	 * DataObject meta info
	 *
	 * @var array
	 */
	protected static $meta = array(
		'tableName' => 'block_sets',
	);
	
	/**
	 * Returns root block of set
	 *
	 * @return \inspirio\webBuilder\cBlockInstance
	 */
	public function getRootBlock()
	{
		if( $this->has( 'blocks' ) === false ) {
			return null;
		}
	
		foreach( $this->get( 'blocks' ) as $block ) {
			/* @var $block Block */
			
			if( $block->parent === null ) {
				return $block;
			}
		}
	
		return null;
	}	
}