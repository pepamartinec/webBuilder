<?php
namespace WebBuilder\Persistance;

use WebBuilder\Administration\TemplateManager\BlockInstanceImporter;

use WebBuilder\DataDependencies\UndefinedData;
use WebBuilder\DataDependencies\InheritedData;
use WebBuilder\DataDependencies\ConstantData;
use WebBuilder\BlockInstance;
use WebBuilder\DataObjects\BlockSet;
use Inspirio\Database\cDatabase;

class DatabaseUpdater
{
	/**
	 * @var \cDatabase
	 */
	protected $database;

	/**
	 * Constructor
	 *
	 * @param \cDatabase $database
	 */
	public function __construct( cDatabase $database )
	{
		$this->database = $database;
	}

	/**
	 * Saves BlockInstances structure
	 *
	 * @param BlockSet $blockSet
	 * @param array $clientData
	 * @return array
	 */
	public function saveBlockInstances( BlockSet $blockSet, array $clientData )
	{
		$blockSetID   = (int)$blockSet->getID();
		$tmpInstances = BlockInstanceImporter::import( $clientData );

		$this->clearDataDependencies( $blockSetID );

		$localInstances = $this->saveInstances( $blockSetID, $tmpInstances );

		$instances = array();
		foreach( $tmpInstances as $instance ) {
			$instances[ $instance->ID ] = $instance;
		}

		$this->loadMissingData( $instances );

		$this->saveInstancesData( $localInstances );

		return $instances;
	}

	/**
	 * Formats the client data same way as the saveBlockInstances method,
	 * but do not actually save iny data to the database
	 *
	 * @param BlockSet $blockSet
	 * @param array $clientData
	 * @return array
	 */
	public function fakeBlockInstances( BlockSet $blockSet, array $clientData )
	{
		$blockSetID = (int)$blockSet->getID();
		$instances  = BlockInstanceImporter::import( $clientData );

		// fake the instance IDs
		foreach( $instances as $tmpID => $instance ) {
			$instance->ID = $tmpID;
		}

		$this->loadMissingData( $instances );
		$this->loadMissingDataDependencies( $instances );

		return $instances;
	}

	private function clearDataDependencies( $blockSetID )
	{
		$sql = "
			DELETE FROM dc
			USING blocks_instances_data_constant dc
			JOIN blocks_instances bi ON ( bi.ID = dc.instance_ID )
			WHERE bi.block_set_ID = {$blockSetID}
		";
		$this->database->query( $sql );

		$sql = "
			DELETE FROM di
			USING blocks_instances_data_inherited di
			JOIN blocks_instances bi ON ( bi.ID = di.instance_ID )
			WHERE bi.block_set_ID = {$blockSetID}
		";
		$this->database->query( $sql );
	}

	private function saveInstances( $blockSetID, array $instances )
	{
		$localInstances = array();

		// destroy all parent-child relations for the current blockSet
		// (including the parent blockSet set links, excluding child blockSets links)
		$sql = "
			DELETE FROM bis
			USING blocks_instances_subblocks bis
			INNER JOIN blocks_instances bi ON ( bi.ID = bis.inserted_instance_ID )
			WHERE bi.block_set_ID = {$blockSetID}
		";
		$this->database->query( $sql );

		// update instances
		foreach( $instances as $tmpID => $instance ) {
			// local blockSet instance, save
			if( $this->isInstanceLocal( $blockSetID, $instance) ) {
				$this->saveInstance( $blockSetID, $instance );

				$localInstances[ $instance->ID ] = $instance;

			// parent blockSet instance, do nothing
			} else {
				if( $instance->ID == null ) {
					throw new \Exception( "Missing ID of the inherited block instance" );
				}
			}
		}

		// remove orphaned instances
		$sql = "DELETE FROM blocks_instances WHERE block_set_ID = {$blockSetID}";
		if( sizeof( $localInstances ) > 0 ) {
			$sql .= ' AND ID NOT IN ('. implode( ',', array_keys( $localInstances ) ) .')';
		}
		$this->database->query( $sql );

		// create parent-child links
		foreach( $instances as $instance ) {
			foreach( $instance->slots as $codeName => $children ) {
				$codeName = $this->database->escape( $codeName );

				foreach( $children as $position => $child ) {
					// skip non-local instances
					if( ! isset( $localInstances[ $child->ID ] ) ) {
						continue;
					}

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

		return $localInstances;
	}

	private function isInstanceLocal( $blockSetID, BlockInstance $instance )
	{
		return $instance->blockSetID == null || $instance->blockSetID == $blockSetID;
	}

	private function saveInstance( $blockSetID, BlockInstance $instance )
	{
		// existing instance
		if( $instance->ID ) {

			// TODO handle the template change when some subblock exist
			// new template may not have the same slots as the original one
			// so the subblock may not fit int theri positions anymore
			$sql = "
				SELECT COUNT(*) `count` FROM blocks_instances bi
				JOIN blocks_instances_subblocks bis ON ( bi.ID = bis.parent_instance_ID  )
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

		$instance->blockSetID = $blockSetID;
	}

	private function loadMissingData( array $instances )
	{
		if( sizeof( $instances ) === 0 ) {
			return;
		}

		$templateIDs = array();
		foreach( $instances as $instance ) {
			$templateIDs[] = $instance->templateID;
		}

		$templateIDsStr = implode( ',', $templateIDs );

		$sql = "
			SELECT
				bt.ID       template_ID,
				bt.filename template_filename,
				b.ID        block_ID,
				b.code_name block_code_name

			FROM blocks_templates bt
			JOIN blocks b ON ( b.ID = bt.block_ID )

			WHERE bt.ID IN ({$templateIDsStr})
		";
		$this->database->query( $sql );
		$resultSet = $this->database->fetchArray();

		$templates = array();
		foreach( $resultSet as $resultItem ) {
			$templateID = (int)$resultItem['template_ID'];

			$templates[ $templateID ] = array(
				'templateFilename' => $resultItem['template_filename'],
				'blockID'          => (int)$resultItem['block_ID'],
				'blockCodeName'    => $resultItem['block_code_name'],
			);
		}

		foreach( $instances as $instance ) { /* @var $instance BlockInstance */
			$templateID = $instance->templateID;

			if( isset( $templates[ $templateID ] ) === false ) {
				// TODO better exception
				throw new \Exception("Invalid template ID '{$templateID}'");
			}

			$template = $templates[ $templateID ];

			$instance->templateFile = $template['templateFilename'];
			$instance->blockID      = $template['blockID'];
			$instance->blockName    = $template['blockCodeName'];
		}
	}

	private function loadMissingDataDependencies( array $instances )
	{
		if( sizeof( $instances ) === 0 ) {
			return;
		}

		$filters = array();
		foreach( $instances as $instance ) {
			$filter = "block_ID = {$instance->blockID}";

			if( sizeof( $instance->dataDependencies ) > 0 ) {
				$properties = array();
				foreach( $instance->dataDependencies as $propertyName => $dependency ) {
					$properties[] = $this->database->escape( $propertyName );
				}

				$filter .= ' AND property NOT IN ("'. implode( '","', $properties ). '")';
			}

			$filters[] = $filter;
		}

		$sql = 'SELECT * FROM blocks_data_requirements WHERE ('. implode( ') OR (', $filters ) .')';
		$this->database->query( $sql );
		$resultSet = $this->database->fetchArray();

		// no additional data requirements
		if( $resultSet == null ) {
			return;
		}

		// group results by blockID
		$blockRequirements = array();
		foreach( $resultSet as $resultItem ) {
			$blockID = (int)$resultItem['block_ID'];

			if( ! isset( $blockRequirements[ $blockID ] ) ) {
				$blockRequirements[ $blockID ] = array();
			}

			$blockRequirements[ $blockID ][ $resultItem['property'] ] = (int)$resultItem['ID'];
		}

		// add UndefinedData to the instances
		foreach( $instances as $instance ) {
			$blockID = $instance->blockID;

			// no additional data requirements
			if( ! isset( $blockRequirements[ $blockID ] ) ) {
				continue;
			}

			foreach( $blockRequirements[ $blockID ] as $property => $propertyID ) {
				if( isset( $instance->dataDependencies[ $property ] ) ) {
					continue;
				}

				$instance->dataDependencies[ $property ] = new UndefinedData( $instance, $propertyID, $property );
			}
		}
	}

	private function saveInstancesData( array $instances )
	{
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
		$provider         = $data->getProvider();
		$providerProperty = $this->database->escape( $data->getProviderProperty() );

		$target         = $data->getTarget();
		$targetProperty = $this->database->escape( $data->getProperty() );

		$sql = "
			INSERT INTO blocks_instances_data_inherited ( instance_ID, provider_instance_ID, provider_property_ID )
			SELECT {$instance->ID}, {$provider->ID}, bdrp.ID
			FROM blocks_data_requirements_providers bdrp
			JOIN blocks_data_requirements bdr ON ( bdr.ID = bdrp.required_property_ID )
				WHERE bdrp.provider_ID = {$provider->blockID}
				  AND bdrp.provider_property = '{$providerProperty}'
				  AND bdr.block_ID = {$target->blockID}
				  AND bdr.property = '{$targetProperty}'
		";
		$this->database->query( $sql );
	}
}