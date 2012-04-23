<?php
namespace Inspirio\Database;

class cDatabase
{
	const FETCH_NUM   = MYSQLI_NUM;
	const FETCH_ASSOC = MYSQLI_ASSOC;
	const FETCH_BOTH  = MYSQLI_BOTH;

	/**
	 * conected database name
	 *
	 * @var string $databaseName
	 */
	protected $databaseName;

	/**
	 * Current connection
	 *
	 * @var \mysqli
	 */
	protected $mysqli;

	/**
	 * Last query result
	 *
	 * @var \mysqli_result
	 */
	protected $result = null;

	/**
	 * Constructor
	 *
	 * @param string $databaseName name of database
	 * @param string $host Host name of the remote database server (if any)
	 * @param string $user User name used to connect to the remote database server
	 * @param string $password Password used to connect to the remote database server
	 *
	 * @throws DatabaseConnectionException DatabaseQueryException
	 */
	public function __construct( $databaseName, $host, $user, $password )
	{
		$this->databaseName = $databaseName;

		// connect to database
		$this->mysqli = new \mysqli( $host, $user, $password, $databaseName ); // @ - may fail
		if( mysqli_connect_errno() ) {
			throw new xConnectionException( 'Database connection failed: '.mysqli_connect_error(), mysqli_connect_errno() );
		}

		// setup charset
		if( $this->mysqli->query( "SET NAMES utf8" ) === false ) { // @ - may fail
			throw new xConnectionException( 'Unable to setup communication charset' );
		}
	}

	/**
	 * Performs SQL query
	 *
	 * @param string $sql SQL Query
	 * @param boolean $multiMode weather there are multiple queries at once
	 * @param boolean $log weather log query into console
	 *
	 * @throws QueryException
	 */
	public function query( $sql )
	{
		if( $this->result instanceof \mysqli_result ) {
			$this->result->free();
		}

		$this->result = $this->mysqli->query( $sql );
		$this->lastQuery = $sql;

		if( $this->result === false ) {
			throw new xQueryException( "Mysql query failed with message: " . $this->mysqli->error, $this->mysqli->errno );
		}
	}

	/**
	 * Returns array of results
	 *
	 * @param integer fetch type
	 * @return array results
	 *
	 * @throws DatabaseFetchException
	 */
	public function fetchArray( $fetchType = self::FETCH_ASSOC )
	{
		if( $this->result instanceof \mysqli_result === false ) {
			throw new xFetchException( 'No result to be proccessed' );
		}

		$results = array();
		while( $result = $this->result->fetch_array( $fetchType ) ) {
			$results[] = $result;
		}

		return $results;
	}

	/**
	 * Returns single result
	 *
	 * @return array result fetch one row from the table as array
	 */
	public function fetchResult()
	{
		if( $this->result instanceof mysqli_result === false ) {
			throw new xFetchException( 'No result to be proccessed' );
		}

		return $this->result->fetch_array( MYSQLI_ASSOC );
	}

	/**
	 * Returns connected database name
	 *
	 * @return string database name
	 */
	public function getDatabaseName()
	{
		return $this->databaseName;
	}

	/**
	 * Returns number of rows affected by last query
	 *
	 * @return int number of rows affected by last query
	 */
	public function getNumRows()
	{
		return $this->mysqli->affected_rows;
	}

	/**
	 * Returns id of last inserted row
	 *
	 * @return int ID of last inserted row
	 */
	public function getLastInsertedId()
	{
		return $this->mysqli->insert_id;
	}

	/**
	 * Closes connection to database
	 *
	 * @return boolean true on success, false otherwise
	 */
	public function close()
	{
		return $this->mysqli->close();
	}

	/**
	 * Starts new transaction
	 *
	 * @throws DatabaseTransactionException
	 */
	public function transactionStart()
	{
		$result = $this->mysqli->autocommit( FALSE );

		if( $result === false ) {
			throw new xTransactionException( 'Unable to start transaction.' );
		}
	}

	/**
	 * Commit started transaction
	 *
	 * @throws DatabaseTransactionException
	 */
	public function transactionCommit()
	{
		$result = $this->mysqli->commit();

		if( $result === false ) {
			throw new xTransactionException( 'Unable to commit transaction.' );
		}

		$result = $this->mysqli->autocommit( TRUE );

		if( $result === false ) {
			throw new xTransactionException( 'Unable to end transaction.' );
		}
	}

	/**
	 * Rollback started transaction
	 *
	 * @throws DatabaseTransactionException
	 */
	public function transactionRollback()
	{
		$result = $this->mysqli->rollback();

		if( $result === false ) {
			throw new xTransactionException( 'Unable to rollback transaction.' );
		}

		$result = $this->mysqli->autocommit( TRUE );

		if( $result === false ) {
			throw new xTransactionException( 'Unable to end transaction.' );
		}
	}

	/**
	 * Escapes string for safe usage in queries
	 *
	 * @param  string $string string to escape
	 * @return string         escaped string
	 */
	public function escape( $string )
	{
		return $this->mysqli->escape_string( $string );
	}
}
