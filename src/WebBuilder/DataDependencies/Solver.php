<?php
namespace WebBuilder\DataDependencies;

use Inspirio\Database\cDatabase;

use Inspirio\Database\cDBFeederBase;
use WebBuilder\BlocksLoaders;
use WebBuilder\BlockInstance;
use WebBuilder\DataDependencyInterface;

/**
 * Automatic data-dependencies solver
 *
 */
class Solver
{
	/**
	 * @var cDatabase
	 */
	protected $database;

	/**
	 * Constructor
	 *
	 * @param cDatabase $database
	 */
	public function __construct( cDatabase $database )
	{
		$this->database = $database;
	}

	/**
	 * Tries to automatically solve missing data-dependencies
	 *
	 * @param array& $instances
	 * @return array List of unresolved dependencies
	 */
	public function solveMissingDependencies( array &$instances )
	{
		// fetch depedencies & providers definition
		$blockIDs = array();

		foreach( $instances as $ID => $instance ) {
			/* @var $instance BlocksInstance */
			$blockIDs[] = $instance->blockID;
		}

		$blockIDsStr = implode( ',', $blockIDs );
		$sql = "
			SELECT
				bdr.ID  property_ID,
				bdrp.ID provider_ID,
				bdrp.provider_ID       provider_block_ID,
				bdrp.provider_property provider_block_property

			FROM blocks_data_requirements bdr
			JOIN blocks_data_requirements_providers bdrp ON ( bdrp.required_property_ID = bdr.ID )

			WHERE bdr.block_ID IN ({$blockIDsStr})
			  AND bdrp.provider_ID IN ({$blockIDsStr})
		";

		$this->database->query( $sql );
		$resultSet = $this->database->fetchArray();

		$providersMap = array();
		if( $resultSet != null ) {
			foreach( $resultSet as $resultItem ) {
				$propertyID       = (int)$resultItem['property_ID'];
				$providerID       = (int)$resultItem['provider_ID'];
				$providerBlockID  = (int)$resultItem['provider_block_ID'];
				$providerProperty = $resultItem['provider_block_property'];

				if( ! isset( $providersMap[ $propertyID ] ) ) {
					$providersMap[ $propertyID ] = array();
				}

				$providersMap[ $propertyID ][ $providerID ] = array( $providerID, $providerProperty );
			}
		}

		// solve dependencies
		$solvedDependencies   = array();
		$unsolvedDependencies = array();

		foreach( $instances as $ID => $instance ) {
			/* @var $instance BlocksInstance */

			foreach( $instance->dataDependencies as &$dependency ) {
				/* @var $dependency DataDependencyInterface */

				if( $dependency instanceof UndefinedData ) {
					$solvedDependency = $this->solveUndefinedData( $providersMap, $instance, $dependency );

					// dependency can be solved
					if( $solvedDependency ) {
						$solvedDependencies[] = $solvedDependency;

						$dependency = new InheritedData( $instance, $dependency->getProperty(), $solvedDependency['provider'], $solvedDependency['property'][1] );

					// unknown dependency solution
					} else {
						$unsolvedDependencies[] = $dependency;
					}
				}
			}
		}

		// save sloved dependencies
		foreach( $solvedDependencies as $dependency ) {
			/* @var InheritedData $dependnecy */
			$targetID   = $dependency['target']->ID;
			$providerID = $dependency['provider']->ID;
			$property   = $dependency['property'][0];

			$sql = "INSERT INTO blocks_instances_data_inherited ( instance_ID, provider_instance_ID, provider_property_ID ) VALUES ( {$targetID}, {$providerID}, '{$property}' )";
			$this->database->query( $sql );
		}

		// return unsolved dependencies
		return $unsolvedDependencies;
	}

	protected function solveUndefinedData( array $providersMap, BlockInstance $target, UndefinedData $dependency )
	{
		$propertyID = $dependency->getPropertyID();

		// no provider exists
		if( isset( $providersMap[ $propertyID ] ) === false ) {
			return null;
		}

		$providerBlocks = $providersMap[ $propertyID ];

		// find provider within tree
		$instance = $target;

		while( $instance->parent !== null ) {
			$instance = $instance->parent;

			if( isset( $providerBlocks[ $instance->blockID ] ) ) {
				return array(
					'target'   => $target,
					'provider' => $instance,
					'property' => $providerBlocks[ $instance->blockID ]
				);
			}
		}

		// no provider found within block parents
		return null;
	}
}
