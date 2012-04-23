<?php
namespace Inspirio\Database;

/**
 * Base data feeder
 *
 * @author Josef Martinec
 * @copyright Copyright (c) 2010, LazyHeads, www.lazyheads.com
 * @version 2.3
 */
class cDBFeederBase
{	
	const LOCK_NONE       = null;
	const LOCK_FOR_READ   = 1;		// MySQL SELECT ... LOCK IN SHARE MODE
	const LOCK_FOR_UPDATE = 2;		// MySQL SELECT ... LOCK FOR UPDATE

	/**
	 * Database connection
	 *
	 * @var cDatabase
	 */
	protected $_database;

	/**
	 * Name of data object
	 *
	 * @var string
	 */
	protected $_className;

	/**
	 * Name of data table
	 *
	 * @var string
	 */
	protected $_tableName;

	/**
	 * List of filters applied to query
	 *
	 * @var array
	 */
	protected $_filters = array();

	/**
	 * Query limit string
	 *
	 * @var string
	 */
	protected $_limit;

	/**
	 * List of orderings
	 *
	 * @var array
	 */
	protected $_orders = array();

	/**
	 * Result index column
	 *
	 * @var string
	 */
	protected $_index = null;

	/**
	 * Result groups keys
	 *
	 * @var array
	 */
	protected $_groups = array();

	/**
	 * Constructor
	 *
	 * @param string $className name of data object
	 * @param cDatabase $database database link
	 */
	public function __construct( $className, cDatabase $database )
	{
		$this->_database  = $database;
		
		$this->_className = $className;
		$this->_tableName = $className::dbGetTableName();
	}

	/**
	 * Returns data class name
	 *
	 * @return string
	 */
	public function className()
	{
		return $this->_className;
	}

	/**
	 * Returns data table name
	 *
	 * @return string
	 */
	public function tableName()
	{
		return $this->_tableName;
	}

	/**
	 * Returns database connection
	 *
	 * @return cDatabase
	 */
	public function database()
	{
		return $this->_database;
	}

// ========== BASE MODIFICATORS ==========

	/**
	 * Appends query filter
	 *
	 * @deprecated 2.3 May be marked as protected in further versions, use column-specific methods instead
	 * @param  string|null $filter valid SQL filter, NULL to reset filters
	 * @return cDBFeederBase
	 */
	public function where( $filter )
	{		
		if( $filter == null ) {
			$this->_filters = array();
			
		} else {
			$this->_filters[] = $filter;
		}

		return $this;
	}

	/**
	 * Appends results offset/count limit
	 *
	 * @param  integer|null $from
	 * @param  integer|null $count
	 * @return cDBFeederBase
	 */
	public function limit( $from = null, $count = null )
	{
		if( $from === null ) {
			$this->_limit = null;

		} else {
			if( $count === null ) {
				$this->_limit = $from;
				
			} else {
				$this->_limit = $from.','.$count;
			}
		}

		return $this;
	}

	/**
	 * Appends results ordering rule
	 *
	 * @param  string $column column to order by, NULL to reset ordering
	 * @param  string|null $direction ordering direction (ASC default)
	 * @return cDBFeederBase
	 */
	public function orderBy( $column, $direction = 'asc' )
	{
		if( strtolower( $direction ) !== 'asc' ) {
			$direction = 'desc';
		}
		
		if( $column == null ) {
			$this->_orders = array();
		} else {
			$column = $this->resolveFullColumnName( $column );
			
			$this->_orders[] = "$column $direction";
		}

		return $this;
	}

	/**
	 * Appends results grouping key
	 *
	 * @param  string $column column used as key, NULL to reset grouping
	 * @return cDBFeederBase
	 */
	public function groupBy( $column )
	{
		if( $column == null ) {
			$this->_groups = array();
		} else {
			$this->_groups[] = $column;
		}

		return $this;
	}

	/**
	 * Sets column used as results index
	 *
	 * @param  string $column column used as key, NULL to clear index
	 * @return cDBFeederBase
	 */
	public function indexBy( $column )
	{
		if( $column == null ) {
			$this->_index = null;
		} else {
			$this->_index = $column;
		}

		return $this;
	}

	/**
	 * Clears all modificators
	 *
	 * @return cDBFeederBase
	 */
	public function clearModificators()
	{
		$this->_filters = array();
		$this->_orders  = array();
		$this->_limit   = null;
		$this->_index   = null;
		$this->_groups  = array();

		return $this;
	}

// ========== ACTIONS ==========

	/**
	 * Executes SELECT query on database,
	 * returns fetched data as plain array
	 *
	 * @param array|string|null $columns  fetches only given columns, if null, all columns (*) are fetched
	 * @param int|null          $lockType applies locking on returned rows (self::LOCK_*)
	 * @return array|null
	 *
	 * @throws DataFeederException
	 */
	public function getRaw( array $columns = null, $lockType = self::LOCK_NONE )
	{
		$className = $this->_className;
		
		if( $columns === null || $columns === '*' ) {
			$columns = '*';

		} else {
			foreach( $columns as $alias => &$column ) {
				$column = $this->resolveFullColumnName( $column );
				
				if( is_int( $alias ) === false ) {
					$column = "{$column} AS {$alias}";
				}
			}

			$columns = implode( ', ', $columns );
		}

		$sql = $this->buildSQL( "SELECT {$columns} FROM `{$this->_tableName}`" );

		switch( $lockType ) {
			case self::LOCK_FOR_UPDATE:
				$sql .= ' LOCK IN SHARED MODE';
				break;
				
			case self::LOCK_FOR_UPDATE:
				$sql .= ' LOCK FOR UPDATE';
				break;
			
			case self::LOCK_NONE:
			default:
				// nothing
				break;
		}
		
		// fetch data
		$this->_database->query( $sql );
		$result = $this->_database->fetchArray();
		
		return sizeof( $result ) > 0 ? $result : null;
	}

	/**
	 * Executes SELECT query on database,
	 * returns fetched data as array of data objects
	 *
	 * @param  int|null   $lockType applies locking on returned rows (self::LOCK_*)
	 * @return array|null
	 *
	 * @throws DataFeederException
	 */
	public function get( $lockType = self::LOCK_NONE )
	{
		return $this->buildObjects( $this->getRaw(), $lockType );
	}

	/**
	 * Executes SELECT query on database
	 * returns first data object of result
	 *
	 * @param  int|null   $lockType applies locking on returned rows (self::LOCK_*)
	 * @return object|null
	 *
	 * @throws DataFeederException
	 */
	public function getOne( $lockType = self::LOCK_NONE )
	{
		$result = $this->limit( 1 )->get( $lockType );

		return $result == null ? null : reset( $result );
	}

	/**
	 * Counts matching rows in database
	 *
	 * @return integer
	 *
	 * @throws DataFeederException
	 */
	public function getCount()
	{
		$result = $this->getRaw( array( 'count' => 'COUNT(*)' ) );

		if( $result === null ) {
			return 0;
		}

		$result = reset( $result );
		return intval( $result['count'] );
	}

	/**
	 * Returns all existing values of given property
	 *
	 * @param  string     $column column name
	 * @return array|null         array of values
	 *
	 * @throws DataFeederException
	 */
	public function getValues( $column )
	{
		$column = $this->resolveFullColumnName( $column );
		
		$result = $this->getRaw( array( 'value' => "DISTINCT {$column}" ) );

		if( $result == null ) {
			return null;
		}

		$array = array();
		foreach( $result as $data ) {
			$array[] = $data['value'];
		}

		return $array;
	}

	/**
	 * Returns min and max value of given property
	 *
	 * @param  string $column column name
	 * @return array          array[ min, max ]
	 *
	 * @throws DataFeederException
	 */
	public function getValuesRange( $column )
	{
		$column = $this->resolveFullColumnName( $column );
		
		$result = $this->getRaw( array(
			'min' => "MIN( {$column} )",
			'max' => "MAX( {$column} )"
		));

		if( $result === null ) {
			return null;
		}

		$result = reset( $result );
		return array( $result['min'], $result['max'] );
	}

	/**
	 * Executes DELETE query on database
	 *
	 * @return integer number of deleted leaves
	 *
	 * @throws xDatabaseException
	 */
	public function remove()
	{
		// check for filters (so we do not delete whole table by a mistake)
		if( sizeof( $this->_filters ) < 1 ) {
			throw new xExecException( 'Using DELETE with no filters is forbidden.' );
		}
		
		$sql = $this->buildSQL( "DELETE FROM {$this->_tableName}" );
		$this->_database->query( $sql );
		
		return $this->_database->getNumRows();
	}
	
	/**
	 * Saves object into database
	 *
	 * @param  mixed      $object
	 * @param  array|null $properties
	 * 
	 * @throws xDatabaseException
	 */
	public function save( $object, array $properties = null )
	{		
		$query = $this->buildSaveQuery( $object, $properties );
		
		$this->_database->query( $query, true, true );
		
		// when adding a new record, update it with its newly assigned ID
		if( $object->getID() == null ) {
			$object->setID( $this->_database->getLastInsertedID() );
		}
	}

// ========== EXTENDED MODIFICATORS ==========

	/**
	 * Prefixes column name with its table name
	 *
	 * @param  string $column
	 * @return string
	 */
	protected function resolveFullColumnName( $column )
	{
		// try to detect non-column (eg. functions) expressions
		if( $column[0] > 'z' || strpos( $column, '(' ) || strpos( $column, ' ' ) ) {
			return $column;
		}
		
		return "`{$this->_tableName}`.`{$column}`";
	}
	
	/**
	 * Adds limitation on column value
	 * SQL: WHERE $column = '$value'
	 *
	 * @deprecated 2.3 Use {@link cDBFeederBase::whereColumnEq} instead
	 * @param  string $column
	 * @param  mixed  $value
	 * @return cDBFeederBase
	 */
	public function whereColumn( $column, $value )
	{
		return $this->whereColumnEq( $column, $value );
	}
	
	/**
	 * Adds limitation on column value
	 * SQL: WHERE $column = '$value'
	 *
	 * @since 2.3
	 *
	 * @param  string $column
	 * @param  mixed  $value
	 * @param  bool   $equals
	 * @return cDBFeederBase
	 */
	public function whereColumnEq( $column, $value, $equals = true )
	{
		$column = $this->resolveFullColumnName( $column );
		
		if( $value === null ) {
			$filter = "{$column} IS NULL";
			
		} else {
			$value = $this->_database->escape( $value );
			$filter = "{$column} = '{$value}'";
		}
		
		if( $equals === false ) {
			$filter = "NOT {$filter}";
		}
		
		return $this->where( $filter );
	}
	
	/**
	 * Adds limitation on column value using given binary operator
	 * SQL: WHERE $column $operator '$value'
	 *
	 * @param  string $column
	 * @param  mixed  $value
	 * @param  string $operator
	 * @param  bool   $equals
	 * @return cDBFeederBase
	 */
	private function whereBinOperator( $column, $value, $operator, $equals )
	{
		$column = $this->resolveFullColumnName( $column );
		$filter = "{$column} {$operator} '{$value}'";
	
		if( $equals === false ) {
			$filter = "NOT {$filter}";
		}
	
		return $this->where( $filter );
	}
	
	/**
	 * Adds limitation on column value
	 * SQL: WHERE $column < '$value'
	 *
	 * @since 2.3
	 *
	 * @param  string $column
	 * @param  mixed  $value
	 * @param  bool   $equals
	 * @return cDBFeederBase
	 */
	public function whereColumnLt( $column, $value, $equals = true )
	{
		return $this->whereBinOperator( $column, $value, '<', $equals );
	}

	/**
	 * Adds limitation on column value
	 * SQL: WHERE $column > '$value'
	 *
	 * @since 2.3
	 *
	 * @param  string $column
	 * @param  mixed  $value
	 * @param  bool   $equals
	 * @return cDBFeederBase
	 */
	public function whereColumnGt( $column, $value, $equals = true )
	{
		return $this->whereBinOperator( $column, $value, '>', $equals );
	}
	
	/**
	 * Adds limitation on column value
	 * SQL: WHERE $column <= '$value'
	 *
	 * @since 2.3
	 *
	 * @param  string $column
	 * @param  mixed  $value
	 * @param  bool   $equals
	 * @return cDBFeederBase
	 */
	public function whereColumnLe( $column, $value, $equals = true )
	{
		return $this->whereBinOperator( $column, $value, '<=', $equals );
	}
	
	/**
	 * Adds limitation on column value
	 * SQL: WHERE $column >= '$value'
	 *
	 * @since 2.3
	 *
	 * @param  string $column
	 * @param  mixed  $value
	 * @param  bool   $equals
	 * @return cDBFeederBase
	 */
	public function whereColumnGe( $column, $value, $equals = true )
	{
		return $this->whereBinOperator( $column, $value, '>=', $equals );
	}
	
	/**
	 * Adds limitation on column value
	 * SQL: WHERE $column LIKE '$value'
	 *
	 * @since 2.3
	 *
	 * @param  string $column
	 * @param  mixed  $value
	 * @param  bool   $equals
	 * @return cDBFeederBase
	 */
	public function whereColumnLike( $column, $value, $equals = true )
	{
		$column = $this->resolveFullColumnName( $column );
		$value  = $this->_database->escape( $value );
		$filter = "{$column} LIKE '{$value}'";
		
		if( $equals !== true ) {
			$filter = "NOT {$filter}";
		}
		
		return $this->where( $filter );
	}
	
	/**
	 * Adds limitation on column value in array
	 * SQL: WHERE column IN ( '$values' )
	 *
	 * @param  string        $column
	 * @param  array|string  $values list of values or subquery
	 * @param  bool          $equals
	 * @return cDBFeederBase
	 */
	public function whereColumnIn( $column, $values, $equals = true )
	{
		if( is_string( $values ) ) {
			$filter = $values;
			
		} elseif( is_array( $values ) ) {
			
			/**
			 * TODO pokud nejsou zadane zadne hodnoty, opravdu preskakovat podminku?
			 * z hlediska psani podminek by odpovidalo 'pokud zaznam nema dany sloupec', cili asi neco jako 'column IS NULL'
			 * z hlediska pouzitelnosti muze nastat nasledujici
			 *  - pouziva se k omezeni dat podle treba nejakych filtru -> nejsou filtry = vynechat podminku (vybrat vse)
			 *  - pouziva se k vyberu dat v navaznosti jen na nektera predchozi data -> nejsou zadna predchozi data = nevybirat nic
			 */
	
			// no values given, skip conditions
			if( $values === null || sizeof( $values ) === 0 ) {
				return $this;
			}
			
			$filter = '';
			foreach( $values as $value ) {
				$value = $this->_database->escape( $value );
					
				$filter .= "'{$value}', ";
			}
			
			$filter = substr( $filter, 0, -2 );
		}
		
		$column = $this->resolveFullColumnName( $column );
		$filter = "{$column} IN ( {$filter} )";
		
		if( $equals !== true ) {
			$filter = "NOT {$filter}";
		}
		
		return $this->where( $filter );
	}

	/**
	 * Adds limitation on column value in given range
	 * SQL: WHERE column BETWEEN lowerBound AND upperBound
	 *
	 * @param  string $column
	 * @param  mixed  $lowerBound
	 * @param  mixed  $upperBound
	 * @param  bool   $equals
	 * @return cDBFeederBase
	 */
	public function whereColumnBetween( $column, $lowerBound, $upperBound, $equals = true )
	{
		$column     = $this->resolveFullColumnName( $column );
		$lowerBound = $this->_database->escape( $lowerBound );
		$upperBound = $this->_database->escape( $upperBound );
		
		$filter = "{$column} BETWEEN '{$lowerBound}' AND '{$upperBound}'";
		
		if( $equals !== true ) {
			$filter = "NOT {$filter}";
		}
		
		return $this->where( $filter );
	}

	/**
	 * Adds limitation on row ID
	 * SQL: WHERE ID = $value
	 *
	 * @param int $value
	 * @return cDBFeederBase
	 */
	public function whereID( $value )
	{
		return $this->whereColumnEq( 'ID', intval( $value ) );
	}

// ========== DATABASE LAYER SPECIFIC DATA FECTH ==========

	protected static function buildSQLColumns( $default, array $columns = null )
	{
		// no columns given, use default pattern
		if( $columns === null || sizeof( $columns ) === 0 ) {
			return $default;
		}

		// append defined aliases
		foreach( $columns as $alias => &$column ) {
			if( !is_int( $alias ) ) {
				$column = $column.' '.$alias;
			}
		}

		return implode( ', ', $columns );
	}

	/**
	 * Build SQL query from given base and set query modificators, modificators are cleared
	 *
	 * @param  string $baseSQL base SQL (SQL clausule part before WHERE statement)
	 * @return string          complete SQL query string
	 */
	protected function buildSQL( $baseSQL )
	{
		$sql = $baseSQL;

		// build filters
		if( sizeof( $this->_filters ) > 0 ) {
			$sql .= ' WHERE ('. implode( ') AND (', $this->_filters ) .') ';
		}

		// append orderings
		if( sizeof( $this->_orders ) > 0 ) {
			$sql .= ' ORDER BY '. implode( ', ', $this->_orders );
		}

		// append limit
		if( $this->_limit !== null ) {
			$sql .= ' LIMIT '. $this->_limit;
		}

		// clear modificators
		$this->_filters = array();
		$this->_orders  = array();
		$this->_limit   = null;

		return $sql;
	}
	
	/**
	 * Builds SQL query for save object
	 *
	 * @param  mixed      $object
	 * @param  array|null $properties
	 * @return string
	 * 
	 * @throws xExecException
	 */
	public function buildSaveQuery( aDataObject $object, array $properties = null )
	{
		if( $object->getID() ) {			
			$query = $this->buildUpdateQuery( $object, $properties );
			
		} else {			
			$query = $this->buildInsertQuery( $object, $properties );
		}
	
		return $query;
	}
	
	/**
	 * Builds UPDATE query for given data object
	 *
	 * @param  iDataObject $object
	 * @param  array|null  $properties
	 * @return string
	 */
	public function buildUpdateQuery( iDataObject $object, array $properties = null )
	{
		$config = $object->getConfiguration();
		$data   = $object->getInnerValues();
		
		$query = "UPDATE `{$this->_tableName}` SET ";
		
		if( $properties === null ) {
			$properties = array_keys( $data );
		}
		
		foreach( $properties as $propertyName ) {
			$property = $config[ $propertyName ];
		
			if( isset( $property['dbColumn'] ) === false ) {
				continue;
			}
			
			if( $propertyName === 'ID' ) {
				continue;
			}
		
			if( isset( $data[ $propertyName ] ) ) {
				$value = $data[ $propertyName ];
			} else {
				$value = null;
			}
		
			if( $value === null ) {
				$value = 'NULL';
			} else {
				$value = "'{$this->_database->escape( $value )}'";
			}
			
			$columnName = $property['dbColumn'];
		
			$query .= "`{$columnName}`={$value}, ";
		}
		
		$query  = substr( $query, 0, -2 );
		$query .= " WHERE `ID` = {$object->get('ID')};";
	
		return $query;
	}
	
	/**
	 * Builds INSERT query for given data object
	 *
	 * @param  iDataObject $object
	 * @param  array|null  $properties
	 * @return string
	 */
	public function buildInsertQuery( iDataObject $object, array $properties = null )
	{
		$config = $object->getConfiguration();
		$data   = $object->getInnerValues();
			
		$query = "INSERT INTO `{$this->_tableName}` SET ";
			
		if( $properties === null ) {
			$properties = array_keys( $data );
		}
			
		foreach( $properties as $propertyName ) {
			$property = $config[ $propertyName ];
				
			if( isset( $property['dbColumn'] ) === false ) {
				continue;
			}
				
			if( isset( $data[ $propertyName ] ) ) {
				$value = $data[ $propertyName ];
			} else {
				$value = null;
			}
				
			if( $value === null ) {
				$value = 'NULL';
			} else {
				$value = "'{$this->_database->escape( $value )}'";
			}
	
			$columnName = $property['dbColumn'];
				
			$query .= "`{$columnName}`={$value}, ";
		}
			
		$query = substr( $query, 0, -2 );
			
		return $query;
	}

	/**
	 * Creates data objects from given query result ans set structure modificators, modificators are cleared
	 *
	 * @param  array|null $qResults query result
	 * @return array|null
	 */
	protected function buildObjects( array $qResult = null )
	{
		if( $qResult !== null ) {
			$dObjects = array();

			// no grouping
			if( $this->_groups === null || sizeof( $this->_groups ) === 0 ) {
				if( $this->_index === null ) {
					foreach( $qResult as $data ) {
						$dObjects[] = $this->createDataObject( $data );
					}

				} else {
					foreach( $qResult as $data ) {
						$dObjects[$data[$this->_index]] = $this->createDataObject( $data );
					}
				}

			// 1 level grouping
			} elseif( sizeof( $this->_groups ) === 1 ) {
				$gk0 = $this->_groups[0];

				if( $this->_index === null ) {
					foreach( $qResult as $data ) {
						$dObjects[$data[$gk0]][] = $this->createDataObject( $data );
					}

				} else {
					foreach( $qResult as $data ) {
						$dObjects[$data[$gk0]][$data[$this->_index]] = $this->createDataObject( $data );
					}
				}

			// 2 levels grouping
			} elseif( sizeof( $this->_groups ) === 2 ) {
				$gk0 = $this->_groups[0];
				$gk1 = $this->_groups[1];

				if( $this->_index === null ) {
					foreach( $qResult as $data ) {
						$dObjects[$data[$gk0]][$data[$gk1]][] = $this->createDataObject( $data );
					}

				} else {
					foreach( $qResult as $data ) {
						$dObjects[$data[$gk0]][$data[$gk1]][$data[$this->_index]] = $this->createDataObject( $data );
					}
				}

			// deeper grouping
			} else {
				$keyString = '';

				foreach( $this->_groups as $level => $group )
					$keyString .= "\$data['{$group}']";

				if( $this->_index === null )
					$keyString .= '[]';
				else
					$keyString .= "[\$data['{$this->_index}']]";

				eval('foreach( $qResult as $data ) {
						$dObjects'.$keyString.' = a$this->createDataObject( $data );
					}');
			}

		// $qResult is NULL
		} else {
			$dObjects = null;
		}

		// clear modificators
		$this->_groups = array();
		$this->_index  = null;

		return $dObjects;
	}
	
	/**
	 * Creates data object from given database data
	 *
	 * @param array $dbData
	 */
	protected function createDataObject( array $dbData )
	{
		$dObject = new $this->_className();
		$dObject->dbSetValues( $dbData );

		return $dObject;
	}
	
	/**
	 * Returns escaped value of given object property
	 *
	 * @deprecated
	 * @param  mixed  $object
	 * @param  string $property
	 * @return mixed
	 */
	public function getSafeValue( iDataObject $object, $property )
	{
		return $this->_database->escape( $object->get( $property ) );
	}
}
