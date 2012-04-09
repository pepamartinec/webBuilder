<?php
namespace WebBuilder\WebBuilder;

/**
 * Web block instance meta data
 *
 * Aggregates tables 'blocks_instances' and 'blocks_instances_subblocks' into
 * appropriate tree structure
 *
 * @author Josef Martinec
 */
class BlockInstance
{
	/**
	 * Parent BlockInstance
	 *
	 * @var WebBuilder\WebBuilder\BlockInstance
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
	 * Exports instance data so it can be reconstructed lately somewhere else
	 *
	 * This method does not export all internal data, only IDs of references
	 *
	 * @return array
	 */
	public function export()
	{
		return self::export_block( $this );
	}

	private static function export_block( BlockInstance $block )
	{
		return array(
			'ID'         => $block->ID,
			'blockID'    => $block->blockID,
			'templateID' => $block->templateID,
			'slots'      => array_map( array( 'self', 'export_blockSlot' ), $block->slots )
		);
	}

	private static function export_blockSlot( $children )
	{
		return array_map( array( 'self', 'export_block' ), $children );
	}
}