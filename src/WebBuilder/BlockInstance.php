<?php
namespace WebBuilder;

use WebBuilder\DataDependencies\InheritedData;
use WebBuilder\DataDependencies\ConstantData;
use ExtAdmin\Request\AbstractRequest;
use WebBuilder\DataDependencies\UndefinedData;

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
	 * Current instance ID
	 *
	 * @var int
	 */
	public $ID;

	/**
	 * The block set ID
	 *
	 * @var int
	 */
	public $blockSetID;

	/**
	 * Parent BlockInstance
	 *
	 * @var WebBuilder\BlockInstance
	 */
	public $parent;

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
		$this->ID               = $instanceID;
		$this->blockSetID       = null;
		$this->parent           = null;
		$this->blockID          = null;
		$this->blockName        = null;
		$this->templateID       = null;
		$this->templateFile     = null;
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
			'blockSetID' => $block->blockSetID,
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

	/**
	 * Creates a block instances from the data received from the client side
	 *
	 * @param array $clientData
	 * @return array
	 */
	public static function import( array $clientData )
	{
		$instanceSet  = array();
		$rootInstance = self::import_instance( $clientData, null, $instanceSet );

		return $instanceSet;
	}

	private static function import_instance( array $clientData, BlockInstance $parent = null, array &$instanceSet )
	{
		// FIXME remove AbstractRequest dependency!!!!
		$instanceID = AbstractRequest::secureData( $clientData, 'ID', 'int' ) ?: null;
		$tmpID      = AbstractRequest::secureData( $clientData, 'tmpID', 'string' ) ?: null;

		if( ! isset( $instanceSet[ $tmpID ] ) ) {
			$instanceSet[ $tmpID ] = new self( null );
		}

		$instance = $instanceSet[ $tmpID ];

		$instance->ID         = $instanceID;
		$instance->blockSetID = AbstractRequest::secureData( $clientData, 'blockSetID', 'int' );
		$instance->parent     = $parent;
		$instance->templateID = AbstractRequest::secureData( $clientData, 'templateID', 'int' );

		// import data
		if( isset( $clientData['data'] ) && is_array( $clientData['data'] ) ) {
			foreach( $clientData['data'] as $rawProperty => $rawValue ) {
				$property = AbstractRequest::secureValue( $rawProperty, 'string' );

				// inherited data
				if( is_array( $rawValue ) ) {
					if( isset( $rawValue['providerID'], $rawValue['providerProperty'] ) ) {
						$providerID       = AbstractRequest::secureValue( $rawValue['providerID'], 'int' );
						$providerProperty = AbstractRequest::secureValue( $rawValue['providerProperty'], 'string' );

						if( $providerID && $providerProperty ) {
							// create dummy provider for now
							if( ! isset( $instanceSet[ $providerID ] ) ) {
								$instances[ $providerID ] = new self( null );
							}

							$instance->dataDependencies[ $property ] = new InheritedData( $instance, $property, $instanceSet[ $providerID ], $providerProperty );
						}
					}

				// constant data
				} else {
					$value = AbstractRequest::secureValue( $rawValue, 'string' );

					if( $value !== '' ) {
						$instance->dataDependencies[ $property ] = new ConstantData( $property, $value );
					}
				}
			}
		}

		// import children
		if( isset( $clientData['slots'] ) && is_array( $clientData['slots'] ) ) {
			foreach( $clientData['slots'] as $rawCodeName => $children ) {
				$codeName = AbstractRequest::secureValue( $rawCodeName, 'string' );

				if( $codeName == null || ! is_array( $children ) ) {
					continue;
				}

				$instance->slots[ $codeName ] = array();
				$slot = &$instance->slots[ $codeName ];

				foreach( $children as $rawChild ) {
					$slot[] = self::import_instance( $rawChild, $instance, $instanceSet );
				}
			}
		}

		return $instance;
	}
}