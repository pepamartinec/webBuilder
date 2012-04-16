<?php
namespace WebBuilder;

/**
 * Web block instance meta data
 *
 * Aggregates tables 'blocks_instances' and 'blocks_instances_subblocks' into
 * appropriate tree structure
 *
 * @author Josef Martinec
 */
use WebBuilder\DataDependencies\UndefinedData;

class BlockInstance
{
	/**
	 * Parent BlockInstance
	 *
	 * @var WebBuilder\BlockInstance
	 */
	public $parent;

	/**
	 * Current instance ID
	 *
	 * @var int
	 */
	public $ID;

	/**
	 * Block ID
	 *
	 * @var int
	 */
	public $blockID;

	/**
	 * WebBlock class name
	 *
	 * @var string
	 */
	public $blockName;

	/**
	 * WebBlock template ID
	 *
	 * @var int
	 */
	public $templateID;

	/**
	 * WebBlock template filename
	 *
	 * @var string
	 */
	public $templateFile;

	/**
	 * Slots occupancy
	 *
	 * @var array|null array( $slotName => array( BlockInstance ) )
	 */
	public $slots;

	/**
	 * Data dependencies against parent
	 *
	 * @var array|null array( BlocksDataDependecy )
	 */
	public $dataDependencies;

	/**
	 * Block instance data
	 *
	 * NULL indicates, that data has not been initialized yet
	 *
	 * @var array|null
	 */
	public $data = null;

	/**
	 * Constructor
	 *
	 * @param int $instanceID
	 */
	public function __construct( $instanceID )
	{
		$this->ID = $instanceID;

		$this->slots            = array();
		$this->dataDependencies = array();
	}

	/**
	 * Adds new child instance to given slot
	 *
	 * @param BlockInstance $childInstance
	 * @param string         $slot
	 * @param integer        $position
	 */
	public function addChild( BlockInstance $childInstance, $slot, $position )
	{
		// touch slot
		if( isset( $this->slots[ $slot ] ) === false ) {
			$this->slots[ $slot ] = array();
		}

		$childInstance->parent = $this;
		$this->slots[ $slot ][ $position ] = $childInstance;
	}

	/**
	 * Returns printable representation of the object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return "{$this->blockName}($this->ID)";
	}

	/**
	 * Returns list of properties that should be persisted on serialization
	 *
	 * @return array
	 */
	public function __sleep()
	{
		return array( 'parent', 'ID', 'blockID', 'blockName', 'templateID', 'templateFile', 'slots', 'dataDependencies' );
	}

	/**
	 * Exports internal data for client-side usage
	 *
	 * @return mixed
	 */
	public function export()
	{
		return $this->export_block( $this );
	}

	/**
	 * Exports BlockInstance content
	 *
	 * @param BlockInstance $block
	 * @return array
	 */
	private static function export_block( BlockInstance $block )
	{
		$data = array(
			'ID'         => $block->ID,
			'blockID'    => $block->blockID,
			'templateID' => $block->templateID,
			'slots'      => array_map( array( 'self', 'export_blockSlot' ), $block->slots ),
			'data'       => array(),
		);

		foreach( $block->dataDependencies as $dependency ) {
			/* @var $dependency DataDependencyInterface */

			if( $dependency instanceof UndefinedData ) {
				continue;
			}

			$data['data'][ $dependency->getProperty() ] = $dependency->export();
		}

		return $data;
	}

	/**
	 * Exports slot content
	 *
	 * @param array $children
	 * @return array
	 */
	private static function export_blockSlot( array $children )
	{
		return array_map( array( 'self', 'export_block' ), $children );
	}
}