<?php
namespace WebBuilder\Administration\TemplateManager;

use WebBuilder\DataDependencies\UndefinedData;

use WebBuilder\DataDependencies\ConstantData;

use WebBuilder\DataDependencies\InheritedData;

use WebBuilder\BlockInstance;

use ExtAdmin\Request\AbstractRequest;

use Inspirio\Database\cDatabase;
use WebBuilder\BlocksLoaders\DatabaseLoader;
use WebBuilder\DataObjects\BlockSet;

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
	public function saveBlockInstances( BlockSet $blockSet, array $clientData )
	{
		$blockSetID = (int)$blockSet->getID();
		$instances  = BlockInstance::import( $clientData );

		$instances = $this->saveInstances( $blockSetID, $instances );

		$this->loadMissingData( $instances );

		$this->saveInstancesData( $instances );

		return $instances;
	}

	private function saveInstances( $blockSetID, array $instances )
	{
		$savedInstances = array();

		// destroy all parent-child relations for the current blockSet
		// (including the parent blockSet set, excluding child blockSets links)
		$sql = "
			DELETE FROM bis
			USING blocks_instances_subblocks bis
			INNER JOIN blocks_instances bi ON ( bi.ID = bis.inserted_instance_ID )
			WHERE bi.block_set_ID = {$blockSetID}
		";
		$this->database->query( $sql );

		// update instances
		foreach( $instances as $tmpID => $instance ) {
			$this->saveInstance( $blockSetID, $instance, $savedInstances );

			$savedInstances[ $instance->ID ] = $instance;
		}

		// remove old instances
		$sql = "DELETE FROM blocks_instances WHERE block_set_ID = {$blockSetID}";
		if( sizeof( $savedInstances ) > 0 ) {
			$sql .= ' AND ID NOT IN ('. implode( ',', array_keys( $savedInstances ) ) .')';
		}
		$this->database->query( $sql );

		return $savedInstances;
	}

	private function saveInstance( $blockSetID, BlockInstance $instance, array &$savedInstances )
	{
		// instance from othe block set
		// do not update
		if( $instance->blockSetID !== $blockSetID ) {
			return;
		}

		// existing instance
		if( $instance->ID ) {
			// TODO handle the template change when some subblock exist
			// new template may not have the same slots as the original one
			// so the subblock may not fit int theri positions anymore
			$sql = "
				SELECT COUNT(*) `count` FROM blocks_instances bi
				JOIN blocks_instances_subblocks bis ON ( bi.ID = bis.parent_slot_ID  )
				WHERE bi.ID = {$instance->ID} AND bi.template_ID != {$instance->templateID}
			";

			$this->database->query( $sql );
			$resultSet = $this->database->fetchArray();
			$result    = reset( $resultSet );
			if( $result['count'] && $result['count'] > 0 ) {
				throw new \Exception('Cannot change the template of block with subblock');
			}

			// update used template file
			$sql = "UPDATE blocks_instances SET template_ID = {$instance->templateID} WHERE ID = {$instance->ID}";
			$this->database->query( $sql );

		// new instance
		} else {
			// create new instance record
			$sql = "INSERT INTO blocks_instances ( block_set_ID, template_ID ) VALUES ( {$blockSetID}, {$instance->templateID} )";
			$this->database->query( $sql );

			$instance->ID = $this->database->getLastInsertedId();
		}

		$savedInstances[ $instance->ID ] = $instance;

		// save subblocks
		foreach( $instance->slots as $codeName => $children ) {
			$codeName = $this->database->escape( $codeName );

			foreach( $children as $position => $child ) {
				// save the child
				$this->saveInstance( $blockSetID, $child, $savedInstances );

				// create the parent-child link
				$sql = "
					INSERT INTO blocks_instances_subblocks ( parent_instance_ID, parent_slot_ID, position, inserted_instance_ID )
					SELECT {$instance->ID}, ID, {$position}, {$child->ID} FROM blocks_templates_slots
					WHERE template_ID = {$instance->templateID} AND code_name = '{$codeName}'
				";
				$this->database->query( $sql );
			}
		}
	}

	private function loadMissingData( array $instances )
	{
		if( sizeof( $instances ) === 0 ) {
			return;
		}

		$instanceIDsStr = implode( ',', array_keys( $instances ) );

		$sql = "
			SELECT
				bi.ID       ID,
				bt.filename template_filename,
				b.ID        block_ID,
				b.code_name block_code_name

			FROM blocks_instances bi
			JOIN blocks_templates bt ON ( bt.ID = bi.template_ID )
			JOIN blocks b ON ( b.ID = bt.block_ID )

			WHERE bi.ID IN ({$instanceIDsStr})
		";
		$this->database->query( $sql );
		$resultSet = $this->database->fetchArray();

		if( sizeof( $resultSet ) !== sizeof( $instances) ) {
			// TODO better exception
			throw new \Exception("Instance count does not match");
		}

		foreach( $resultSet as $resultItem ) {
			$instanceID = (int)$resultItem['ID'];

			if( isset( $instances[ $instanceID ] ) === false ) {
				// TODO better exception
				throw new \Exception("Loaded data does not match instances");
			}

			/* @var $instance BlockInstance */
			$instance = $instances[ $instanceID ];
			$instance->templateFile = $resultItem['template_filename'];
			$instance->blockID      = (int)$resultItem['block_ID'];
			$instance->blockName    = $resultItem['block_code_name'];
		}
	}

	private function saveInstancesData( array $instances )
	{
		// remove old data
		$instanceIDsStr = implode( ',', array_keys( $instances ) );

		$sql = "DELETE FROM blocks_instances_data_constant WHERE instance_ID IN ({$instanceIDsStr})";
		$this->database->query( $sql );

		$sql = "DELETE FROM blocks_instances_data_inherited WHERE instance_ID IN ({$instanceIDsStr})";
		$this->database->query( $sql );

		// save new data
		foreach( $instances as $instance ) {
			/* @var $instance BlockInstance */

			foreach( $instance->dataDependencies as $dependency ) {
				if( $dependency instanceof ConstantData ) {
					$this->saveInstanceData_constant( $instance, $dependency );

				} elseif( $dependency instanceof InheritedData ) {
					$this->saveInstanceData_inherited( $instance, $dependency );

				} elseif( $dependency instanceof UndefinedData ) {
					// do nothing

				} else {
					// TODO better exception
					throw new \Exception("Invalid data dependency");
				}
			}
		}
	}

	private function saveInstanceData_constant( BlockInstance $instance, ConstantData $data )
	{
		$value    = $this->database->escape( $data->getTargetData() );
		$property = $this->database->escape( $data->getProperty() );

		$sql = "
			INSERT INTO blocks_instances_data_constant ( instance_ID, property_ID, value )
			SELECT {$instance->ID}, ID, '{$value}' FROM blocks_data_requirements
				WHERE block_ID = {$instance->blockID} AND property = '{$property}'
		";
		$this->database->query( $sql );
	}

	private function saveInstanceData_inherited( BlockInstance $instance, InheritedData $data )
	{
		$provider = $data->getProvider();
		$property = $this->database->escape( $data->getProviderProperty() );

		$sql = "
			INSERT INTO blocks_instances_data_inherited ( instance_ID, provider_instance_ID, provider_property_ID )
			SELECT {$instance->ID}, {$provider->ID}, ID FROM blocks_data_requirements_providers
				WHERE provider_ID = {$provider->blockID} AND provider_property = '{$property}'
		";
		$this->database->query( $sql );
	}
}