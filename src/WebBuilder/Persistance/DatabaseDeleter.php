<?php
namespace WebBuilder\Persistance;

use Inspirio\Database\xDatabaseException;

use Inspirio\Database\cDatabase;

class DatabaseDeleter
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
	 * Returns inherited blockSets count
	 *
	 * @param array $blockSetIDs
	 * @return array
	 */
	public function getInheritedBlockSetCount( array $blockSetIDs )
	{
		$blockSetFilter = 'bs.ID IN ('. implode( ',', $blockSetIDs ) .')';
		$count          = array_fill_keys( $blockSetIDs, 0 );

		$sql = "
			SELECT
				bs.ID ID,
				COUNT( bs.ID ) count
			FROM block_sets bs
			JOIN block_sets bsc ON ( bsc.parent_ID = bs.ID )
			WHERE {$blockSetFilter}
			GROUP BY bs.ID
		";

		$this->database->query( $sql );
		$resultSet = $this->database->fetchArray();

		// no result, every blockSet can be deleted
		if( $resultSet == null ) {
			return $count;
		}

		foreach( $resultSet as $resultItem ) {
			$blockSetID = $resultItem['ID'];

			if( ! isset( $count[ $blockSetID ] ) ) {
				continue;
			}

			$count[ $blockSetID ] = (int)$resultItem['count'];
		}

		return $count;
	}

	/**
	 * Returns webPage usage count of the blockSets
	 *
	 * @param array $blockSetIDs
	 * @return array
	 */
	public function getWebPageUsageCount( array $blockSetIDs )
	{
		$blockSetFilter = 'bs.ID IN ('. implode( ',', $blockSetIDs ) .')';
		$count          = array_fill_keys( $blockSetIDs, 0 );

		$sql = "
			SELECT
				bs.ID ID,
				COUNT( bs.ID ) count
			FROM block_sets bs
			JOIN web_pages wp ON ( wp.block_set_ID = bs.ID )
			WHERE {$blockSetFilter}
			GROUP BY bs.ID
		";

		$this->database->query( $sql );
		$resultSet = $this->database->fetchArray();

		// no result, every blockSet can be deleted
		if( $resultSet == null ) {
			return $count;
		}

		foreach( $resultSet as $resultItem ) {
			$blockSetID = $resultItem['ID'];

			if( ! isset( $count[ $blockSetID ] ) ) {
				continue;
			}

			$count[ $blockSetID ] = (int)$resultItem['count'];
		}

		return $count;
	}

	/**
	 * Clears any data inside of the blockSets
	 *
	 * TODO this method also exists in the DatabaseUpdater
	 *
	 * @param array $blockSetIDs
	 *
	 * @throws xDatabaseException
	 */
	private function clearDataDependencies( array $blockSetIDs )
	{
		$blockSetFilter = 'bi.block_set_ID IN ('. implode( ',', $blockSetIDs ) .')';

		$sql = "
			DELETE FROM dc
			USING blocks_instances_data_constant dc
			JOIN blocks_instances bi ON ( bi.ID = dc.instance_ID )
			WHERE {$blockSetFilter}
		";
		$this->database->query( $sql );

		$sql = "
			DELETE FROM di
			USING blocks_instances_data_inherited di
			JOIN blocks_instances bi ON ( bi.ID = di.instance_ID )
			WHERE {$blockSetFilter}
		";
		$this->database->query( $sql );
	}

	/**
	 * Deletes block
	 *
	 * @param array $blockSetIDs
	 *
	 * @throws xDatabaseException
	 */
	public function deleteBlockInstances( array $blockSetIDs )
	{
		// clear the dataDependencies first
		// otherwise we can get data constraint exceptions
		$this->clearDataDependencies( $blockSetIDs );

		// delete blockSets including associated block instances
		$blockSetFilter = 'ID IN ('. implode( ',', $blockSetIDs ) .')';

		$sql = "DELETE FROM block_sets WHERE {$blockSetFilter}";
		$this->database->query( $sql );
	}
}