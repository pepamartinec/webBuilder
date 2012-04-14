<?php
namespace WebBuilder\WebBuilder\ExtAdmin\TemplatesManager;

use ExtAdmin\Request\AbstractRequest;

use Inspirio\Database\cDatabase;
use WebBuilder\WebBuilder\BlocksLoaders\DatabaseLoader;
use WebBuilder\WebBuilder\DataObjects\BlocksSet;

use ExtAdmin\RequestInterface;

class BlockInstancesUpdater
{
	const SLOT_KEY_ID       = 'id';
	const SLOT_KEY_CODENAME = 'codeName';

	const DATA_KEY_ID       = 'id';
	const DATA_KEY_CODENAME = 'codeName';

	/**
	 * @var \cDatabase
	 */
	protected $database;

	/**
	 * @var string
	 */
	protected $slotKey;

	/**
	 * @var string
	 */
	protected $dataKey;

	/**
	 * Constructor
	 *
	 * @param \cDatabase $database
	 */
	public function __construct( cDatabase $database, array $config = null )
	{
		$this->database = $database;

		// apply config
		if( $config === null ) {
			$config = array();
		}

		$config += array(
			'slotKey' => self::SLOT_KEY_CODENAME,
			'dataKey' => self::DATA_KEY_CODENAME
		);

		$this->setSlotKey( $config['slotKey'] );
		$this->setDataKey( $config['dataKey'] );
	}

	/**
	 * Sets the slot key config
	 *
	 * @param string $slotKey TemplateUpdater::SLOT_KEY_*
	 * @return TemplateUpdater
	 */
	public function setSlotKey( $slotKey )
	{
		// check value
		$valid = array( self::SLOT_KEY_CODENAME, self::SLOT_KEY_ID );

		if( ! in_array( $slotKey, $valid ) ) {
			throw new \Exception( "Invalid slot key type '{$slotKey}'" );
		}

		// setup value
		$this->slotKey = $slotKey;

		return $this;
	}

	/**
	 * Sets the data key config
	 *
	 * @param string $dataKey TemplateUpdater::DATA_KEY_*
	 * @return TemplateUpdater
	 */
	public function setDataKey( $dataKey )
	{
		// check value
		$valid = array( self::DATA_KEY_CODENAME, self::DATA_KEY_ID );

		if( ! in_array( $dataKey, $valid ) ) {
			throw new \Exception( "Invalid data key type '{$dataKey}'" );
		}

		// setup value
		$this->dataKey = $dataKey;

		return $this;
	}

	/**
	 * Saves BlockInstances structure
	 *
	 * @param RequestInterface $request
	 * @return array
	 */
	public function saveBlockInstances( BlocksSet $blocksSet, array $clientData )
	{
		// secure user data
		$rootInstance = $this->secureClientData( $clientData );

		// convert slot && data identificators
		$this->convertSlotIdentificators( $rootInstance );
		$this->convertDataIdentificators( $rootInstance );

		// save blocks instances
		$validInstanceIDs = array();
		$this->writeBlockInstances( $blocksSet->getID(), $rootInstance, $validInstanceIDs );

		$sql = "DELETE FROM blocks_instances WHERE blocks_set_ID = {$blocksSet->getID()}";
		if( sizeof( $validInstanceIDs ) > 0 ) {
			$sql .= ' AND ID NOT IN ('. implode( ',', $validInstanceIDs ) .')';
		}
		$this->database->query( $sql );

		return $rootInstance;
	}

	/**
	 * Secures the BlockInstances structure received from the client side
	 *
	 * @param array $rawBlock
	 * @return array
	 */
	private function secureClientData( array $rawBlock )
	{
		// FIXME remove AbstractRequest dependency!!!!
		$instance = array(
			'ID'         => AbstractRequest::secureData( $rawBlock, 'ID', 'int' ) ?: null,
			'templateID' => AbstractRequest::secureData( $rawBlock, 'templateID', 'int' ),
			'data'       => array(),
			'slots'      => array(),
		);

		$instances[] = &$instance;

		if( isset( $rawBlock['data'] ) ) {
			foreach( $rawBlock['data'] as $rawProperty => $rawValue ) {
				// FIXME handle also inherited data ( object[ $property => $providerID ] )
				if( ! is_string( $rawValue ) ) {
					continue;
				}

				$property = AbstractRequest::secureValue( $rawProperty, 'string' );
				$value    = AbstractRequest::secureValue( $rawValue, 'string' );

				$instance['data'][ $property ] = $value;
			}
		}

		if( isset( $rawBlock['slots'] ) ) {
			foreach( $rawBlock['slots'] as $rawSlotID => $children ) {
				$slotID = AbstractRequest::secureValue( $rawSlotID, 'string' );

				$instance['slots'][ $slotID ] = array();
				$slot = &$instance['slots'][ $slotID ];

				foreach( $children as $rawChild ) {
					$slot[] = $this->secureClientData( $rawChild );
				}
			}
		}

		return $instance;
	}

	/**
	 * Converts codeName slot identificators, used on the client side, to IDs
	 *
	 * @param array& $rootInstance
	 */
	private function convertSlotIdentificators( array &$rootInstance )
	{
		if( $this->slotKey === self::SLOT_KEY_ID ) {
			return;
		}

		// collect used codeNames
		$slotCodeNames = array();
		$this->convertSlotIdentificators_collectSlots( $rootInstance, $slotCodeNames );

		// load slots
		$filter = array();
		foreach( $slotCodeNames as $templateID => $codeNames ) {
			$codeNames = implode( "','", $codeNames );

			$filter[] = "( template_ID = {$templateID} AND code_name IN ('{$codeNames}') )";
		}

		$filterStr = implode( ' OR ', $filter );
		$sql = "SELECT * FROM blocks_templates_slots WHERE {$filterStr}";
		$this->database->query( $sql );
		$resultSet = $this->database->fetchArray();

		// build codeName -> ID map
		$slotMap = array();
		if( $resultSet != null ) {
			foreach( $resultSet as $resultItem ) {
				$ID         = (int)$resultItem['ID'];
				$codeName   = $resultItem['code_name'];
				$templateID = (int)$resultItem['template_ID'];

				if( ! isset( $slotMap[ $templateID ] ) ) {
					$slotMap[ $templateID ] = array();
				}

				$slotMap[ $templateID ][ $codeName ] = $ID;
			}
		}

		// convert identificators
		$this->convertSlotIdentificators_convert( $rootInstance, $slotMap );
	}

	private function convertSlotIdentificators_collectSlots( array $instance, array &$slots )
	{
		// skip blocks with no slots
		if( $instance['slots'] == null ) {
			return;
		}

		$templateID = $instance['templateID'];

		foreach( $instance['slots'] as $codeName => $children ) {
			$slots[ $templateID ][] = $codeName;

			foreach( $children as $child ) {
				$this->convertSlotIdentificators_collectSlots( $child, $slots );
			}
		}
	}

	private function convertSlotIdentificators_convert( array &$instance, array $slotMap )
	{
		// skip blocks with no slots
		if( $instance['slots'] == null ) {
			return;
		}

		$templateID = $instance['templateID'];

		if( isset( $slotMap[ $templateID ] ) === false ) {
			// ERROR
		}

		$templateSlots = $slotMap[ $templateID ];
		$slotsByIDs    = array();

		foreach( $instance['slots'] as $codeName => $children ) {
			if( isset( $templateSlots[ $codeName ] ) === false ) {
				// ERROR
				$a = 1;
			}

			$slotID = $templateSlots[ $codeName ];

			$slotsByIDs[ $slotID ] = array();
			$newChildren = &$slotsByIDs[ $slotID ];

			foreach( $children as &$child ) {
				$this->convertSlotIdentificators_convert( $child, $slotMap );

				$newChildren[] = $child;
			}
		}

		$instance['slots'] = $slotsByIDs;
	}

	/**
	 * Converts codeName data identificators, used on the client side, to IDs
	 *
	 * @param array& $rootInstance
	 */
	private function convertDataIdentificators( array &$rootInstance )
	{
		if( $this->dataKey === self::DATA_KEY_ID ) {
			return;
		}

		// collect used codeNames
		$dataProperties = array();
		$this->convertDataIdentificators_collectData( $rootInstance, $dataProperties );

		if( sizeof( $dataProperties ) === 0 ) {
			return;
		}

		// load property IDs
		$filter = array();
		foreach( $dataProperties as $templateID => $properties ) {
			$propertyStr = implode( "','", array_map( array( $this->database, 'escape' ), $properties ) );

			$filter[] = "( bt.ID = {$templateID} AND bdr.property IN ('{$propertyStr}') )";
		}

		$filterStr = implode( ' OR ', $filter );
		$sql = "
			SELECT
				bdr.property,
				bdr.ID,
				bt.ID template_ID

			FROM blocks_data_requirements bdr
			JOIN blocks_templates bt ON ( bt.block_ID = bdr.block_ID )

			WHERE {$filterStr}
		";
		$this->database->query( $sql );
		$resultSet = $this->database->fetchArray();

		// build property -> ID map
		$propertyMap = array();
		if( $resultSet != null ) {
			foreach( $resultSet as $resultItem ) {
				$ID         = (int)$resultItem['ID'];
				$property   = $resultItem['property'];
				$templateID = (int)$resultItem['template_ID'];

				if( ! isset( $propertyMap[ $templateID ] ) ) {
					$propertyMap[ $templateID ] = array();
				}

				$propertyMap[ $templateID ][ $property ] = $ID;
			}
		}

		// convert identificators
		$this->convertDataIdentificators_convert( $rootInstance, $propertyMap );
	}

	private function convertDataIdentificators_collectData( array $instance, array &$data )
	{
		// skip blocks with no slots
		if( $instance['data'] != null ) {
			$templateID = $instance['templateID'];

			if( ! isset( $data[ $templateID ] ) ) {
				$data[ $templateID ] = array();
			}

			$data[ $templateID ] = array_merge( $data[ $templateID ], array_keys( $instance['data'] ) );
		}

		if( isset( $instance['slots'] ) ) {
			foreach( $instance['slots'] as $children ) {
				foreach( $children as $child ) {
					$this->convertDataIdentificators_collectData( $child, $data );
				}
			}
		}
	}

	private function convertDataIdentificators_convert( array &$instance, array $propertyMap )
	{
		if( $instance['data'] != null ) {
			$templateID = $instance['templateID'];

			if( ! isset( $propertyMap[ $templateID ] ) ) {
				// ERROR
			}

			$templateData = $propertyMap[ $templateID ];
			$dataByIDs    = array();

			foreach( $instance['data'] as $property => $value ) {
				if( ! isset( $templateData[ $property ] ) ) {
					// ERROR
					continue;
				}

				$propertyID = $templateData[ $property ];

				$dataByIDs[ $propertyID ] = $value;
			}

			$instance['data'] = $dataByIDs;
		}

		if( isset( $instance['slots'] ) ) {
			foreach( $instance['slots'] as &$children ) {
				foreach( $children as &$child ) {
					$this->convertDataIdentificators_convert( $child, $propertyMap );
				}
			}
		}
	}

	/**
	 * Saves proccessed BlockInstances
	 *
	 * @param int $blocksSetID
	 * @param array& $instance
	 * @param array& $validInstanceIDs
	 */
	private function writeBlockInstances( $blocksSetID, array &$instance, array &$validInstanceIDs )
	{
		// save block instance
		if( $instance['ID'] ) {
			$sql  = "UPDATE blocks_instances SET template_ID = {$instance['templateID']} WHERE ID = {$instance['ID']}";
			$this->database->query( $sql );

			$sql = "DELETE FROM blocks_instances_subblocks WHERE parent_instance_ID = {$instance['ID']}";
			$this->database->query( $sql );

			$sql = "DELETE FROM blocks_instances_data_constant WHERE instance_ID = {$instance['ID']}";
			$this->database->query( $sql );

		} else {
			$sql = "INSERT INTO blocks_instances ( blocks_set_ID, template_ID ) VALUES ( {$blocksSetID}, {$instance['templateID']} )";
			$this->database->query( $sql );

			$instance['ID'] = $this->database->getLastInsertedId();
		}

		$validInstanceIDs[] = $instance['ID'];

		// save constant data
		foreach( $instance['data'] as $propertyID => $value ) {
			$value = $this->database->escape( $value );

			$sql = "INSERT INTO blocks_instances_data_constant ( instance_ID, property_ID, value ) VALUES ( {$instance['ID']}, {$propertyID}, '{$value}' )";
			$this->database->query( $sql );
		}

		// save subblocks
		foreach( $instance['slots'] as $slotID => $children ) {
			foreach( $children as $position => &$child ) {
				$this->writeBlockInstances( $blocksSetID, $child, $validInstanceIDs );

				$sql = "INSERT INTO blocks_instances_subblocks ( parent_instance_ID, parent_slot_ID, position, inserted_instance_ID ) VALUES ( {$instance['ID']}, {$slotID}, {$position}, {$child['ID']} )";
				$this->database->query( $sql );
			}
		}
	}
}