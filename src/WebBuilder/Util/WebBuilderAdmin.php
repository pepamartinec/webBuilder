<?php
namespace WebBuilder\Admin;

class WebBuilderAdmin
{
	/**
	 * Database connection
	 *
	 * @var \Database
	 */
	protected $database;

	/**
	 * Constructor
	 *
	 * @param Database $database
	 */
	public function __construct( \Database $database )
	{
		$this->database = $database;
	}

	public function generateInstancesDataDependencies()
	{
		$this->database->query("
			SELECT
				instances.*,
				blocks.ID        block_ID,
				blocks.code_name block_name

			FROM blocks_instances instances
			INNER JOIN blocks_templates templates ON ( instances.template_ID = templates.ID )
			INNER JOIN blocks ON ( templates.block_ID = blocks.ID )
		");
		$instances = $this->database->fetchArray();

		// no instances defined
		if( $instances == null ) {
			return;
		}

		// pre-load properties providers
		$propertiesProviders = $this->fetchDataPropertiesProviders();

		foreach( $instances as $instance ) {
			// load instance data requirements
			$this->database->query("
				SELECT
					requirements.*

				FROM blocks_instances instances
				INNER JOIN blocks_templates templates ON ( instances.template_ID = templates.ID )
				INNER JOIN blocks_data_requirements requirements ON ( templates.block_ID = requirements.block_ID )

				WHERE instances.ID = {$instance['ID']}
			");
			$result = $this->database->fetchArray();

			// no data required by this instance
			if( $result == null ) {
				$this->database->query("
					DELETE FROM blocks_instances_data_constant  WHERE instance_ID = {$instance['ID']};
					DELETE FROM blocks_instances_data_inherited WHERE instance_ID = {$instance['ID']};
				", true );

				continue;
			}

			$requiredProperties = array();
			foreach( $result as $item ) {
				$requiredProperties[ $item['ID'] ] = $item;
			}

			// load existing data-propagation definitions
			$this->database->query("
				(
					-- INHERITED DATA --
					SELECT properties.ID property_ID
					FROM blocks_instances_data_inherited dependency
					INNER JOIN blocks_data_requirements_providers providers ON ( dependency.provider_property_ID = providers.ID )
					INNER JOIN blocks_data_requirements properties ON ( providers.required_property_ID = properties.ID )
					WHERE dependency.instance_ID = {$instance['ID']}

				) UNION (

					-- CONSTANT DATA --
					SELECT property_ID
					FROM blocks_instances_data_constant
					WHERE instance_ID = {$instance['ID']}
				)
			");
			$result = $this->database->fetchArray();

			$existingProviders = array();
			if( $result != null ) {
				foreach( $result as $item ) {
					$existingProviders[ $item['property_ID'] ] = $item;
				}
			}

			$unprovidedProperties = array_diff_key( $requiredProperties, $existingProviders );

			// every data $unprovidedProperties has its data provider
			if( sizeof( $unprovidedProperties ) === 0 ) {
				continue;
			}

			// load instance parents
			$instanceParents = $this->fetchInstancePredecessors( $instance['ID'] );

			$query = '';
			foreach( $unprovidedProperties as $property ) {
				$propertyProviderID = null;

				foreach( $instanceParents as $parentInstanceID ) {
					if( isset( $propertiesProviders[ $property['ID'] ][ $parentInstanceID ] ) ) {
						$propertyProviderID = $propertiesProviders[ $property['ID'] ][ $parentInstanceID ];
						break;
					}
				}

				if( $propertyProviderID === null ) {
					continue;
					throw new \Exception("Missing property '{$property['property']}' provider for block '{$instance['block_name']}({$instance['ID']})'");
				}

				$query .= "
					INSERT INTO blocks_instances_data_inherited ( instance_ID, provider_instance_ID, provider_property_ID )
					VALUES ( {$instance['ID']}, {$parentInstanceID}, {$propertyProviderID} );
				";
			}

			if( $query !== '' ) {
				$this->database->transactionStart();
				$this->database->query( $query, true );
				$this->database->transactionCommit();
			}
		}
	}

	private function fetchInstancePredecessors( $instanceID )
	{
		$this->database->query("
			SELECT
				parent_instance_ID

			FROM blocks_instances_subblocks
			WHERE inserted_instance_ID = {$instanceID}
		");
		$result = $this->database->fetchArray();

		if( $result != null ) {
			$parentInstanceID = $result[0]['parent_instance_ID'];

			$predecessors = $this->fetchInstancePredecessors( $parentInstanceID );
			array_unshift( $predecessors, $parentInstanceID );

			return $predecessors;

		} else {
			return array();
		}
	}

	private function fetchDataPropertiesProviders()
	{
		$providers = array();

		$this->database->query("
			SELECT
				requirements.ID              property_ID,
				providers.ID                 provider_ID,
				providers_instances.ID       instance_ID

			FROM       blocks_data_requirements           requirements
			INNER JOIN blocks_data_requirements_providers providers    ON ( requirements.ID = providers.required_property_ID )

			INNER JOIN blocks_templates providers_templates ON ( providers.provider_ID = providers_templates.block_ID )
			INNER JOIN blocks_instances providers_instances ON ( providers_templates.ID = providers_instances.template_ID )
		");
		$result = $this->database->fetchArray();

		if( $result != null ) {
			foreach( $result as $item ) {
				if( isset( $providers[ $item['property_ID'] ] ) === false ) {
					$providers[ $item['property_ID'] ] = array();
				}

				$providers[ $item['property_ID'] ][ $item['instance_ID'] ] = $item['provider_ID'];
			}
		}

		return $providers;
	}

	/**
	* Sets data dependencies for given block
	*
	* @param int        $blockID              block ID
	* @param array|null $parentsDependencies  array( $parentBlockID => array( BlocksDependecyItem ) ) | NULL to preserve current data
	* @param array|null $childrenDependencies array( $childBlockID => array( BlocksDependecyItem ) ) | NULL to preserve current data
	*
	* @throws DataFeederException
	*/
	public function updateBlockDataDependencies( $blockID, array $parentsDependencies = null, array $childrenDependencies = null )
	{
		if( $parentsDependencies === null && $childrenDependencies === null ) {
			return;
		}

		$query = '';

		if( $parentsDependencies !== null ) {
			try {
				$this->database->query( "SELECT ID, property FROM blocks_data_requirements WHERE block_ID = {$blockID}" );
				$result = $this->database->fetchArray();

			} catch( DatabaseException $e ) {
				throw new DataFeederException( $e->getMessage(), $e->getCode() );
			}

			$blockProperties = array();
			foreach( $result as $item ) {
				$blockProperties[ $item['property'] ] = $item['ID'];
			}

			$query .= 'DELETE FROM blocks_data_requirements_providers WHERE required_property_ID IN ('.implode(',', $blockProperties).');';

			foreach( $parentsDependencies as $parentBlockID => $parentDependencies ) {
				foreach( $parentDependencies as $parentDependency ) {
					$sourceData = $this->database->escape( $parentDependency['sourceData'] );
					$targetData = $this->database->escape( $parentDependency['targetData'] );

					if( $sourceData == null || $targetData == null || isset( $blockProperties[ $targetData ] ) === false ) {
						continue;
					}

					$query .= "INSERT INTO blocks_data_requirements_providers ( required_property_ID, provider_ID, provider_property ) VALUES ( {$blockProperties[$targetData]}, {$parentBlockID}, '{$sourceData}' );";
				}
			}
		}

		if( $childrenDependencies !== null ) {
			$query .= "DELETE FROM blocks_data_requirements_providers WHERE provider_ID = {$blockID};";

			foreach( $childrenDependencies as $childBlockID => $childDependencies ) {
				foreach( $childDependencies as $childDependency ) {
					$sourceData = $this->database->escape( $childDependency['sourceData'] );
					$targetData = $this->database->escape( $childDependency['targetData'] );

					if( $sourceData == null || $targetData == null ) {
						continue;
					}

					$query .= "
								INSERT INTO blocks_data_requirements_providers ( required_property_ID, provider_ID, provider_property )
								SELECT ID, {$blockID}, '{$sourceData}' FROM blocks_data_requirements WHERE block_ID = {$childBlockID} AND property = '{$targetData}';
							";
				}
			}
		}

		$this->database->transactionStart();
		try {
			$this->database->query( $query, true );

		} catch( DatabaseException $e ) {
			$this->database->transactionRollback();
			throw new DataFeederException( $e->getMessage(), $e->getCode() );
		}
		$this->database->transactionCommit();
	}
}