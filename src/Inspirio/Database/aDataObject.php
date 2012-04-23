<?php
namespace Inspirio\Database;

abstract class aDataObject implements iDataObject
{
	protected static $items = null;
	
	protected static $meta = null;
	
	/**
	 * @property array internal data storage
	 */
	protected $data;
	
	/**
	 * Data object constructor
	 *
	 * @param array $values
	 * @param bool  $sanitize
	 */
	public function __construct( array $values = null, $sanitize = false )
	{
		$this->setInnerValues( $values, $sanitize );
	}
	
	/**
	 * Universal property values setter
	 *
	 * @param string $property
	 * @param mixed  $value
	 * @param bool   $sanitize
	 *
	 * @throws xInvalidPropertyException
	 */
	public function set( $property, $value, $sanitize = false )
	{
		if( isset( static::$items[ $property ] ) === false ) {
			throw new xInvalidPropertyException( $this, $property );
		}
		
		if( $sanitize === true ) {
			$value = $this->sanitize( $property, $value );
		}
		
		$this->data[ $property ] = $value;
	}
	
	/**
	 * Universal property getter
	 *
	 * @param  string $property
	 * @return mixed
	 *
	 * @throws xInvalidPropertyException
	 */
	public function get( $property )
	{
		if( isset( static::$items[ $property ] ) === false ) {
			throw new xInvalidPropertyException( $this, $property );
		}

		if( isset( $this->data[ $property ] ) ) {
			return $this->data[ $property ];
			
		} else {
			return null;
		}
	}
	
	/**
	 * Universal property value existance checker
	 *
	 * Returns TRUE when property has value (even NULL)
	 *
	 * @param  string $property
	 * @return bool
	 */
	public function has( $property )
	{
		return isset( $this->data[ $property ] );
	}
	
	/**
	 * Magic getters/setters handler
	 *
	 * @param string $name      method name
	 * @param array  $arguments method arguments
	 *
	 * @throws xInvalidMethodException
	 * @throws xInvalidPropertyException
	 */
	public function __call( $name, array $arguments = null )
	{
		$prefix = substr( $name, 0, 3 );
		$var    = lcfirst( substr( $name, 3 ) );
		$value  = $arguments ? $arguments[0] : null;
		
		switch( $prefix ) {
			case 'set': $this->set( $var, $value ); return;
			case 'get': return $this->get( $var );
			case 'has': return $this->has( $var );
				
			default:
				if( isset( static::$items[ $name ] ) === false ) {
					throw new xInvalidMethodException( $this, $name );
				}
				
				if( $arguments ) {
					$this->set( $name, $arguments[0] );
					return;
					
				} else {
					return $this->get( $name );
				}
		}
	}
	
	/**
	 * Clears all values in object
	 *
	 */
	public function clearValues()
	{
		$this->data = array();
	}
	
	/**
	 * Returns all object values
	 *
	 * @return array
	 */
	public function getInnerValues()
	{
		return $this->data;
	}
	
	/**
	 * Replaces object data
	 *
	 * @param array|null $values   data to set
	 * @param bool       $sanitize if true, data will be sanitized
	 * @param array      $items    list of items from $values list, that should be stored
	 */
	public function setInnerValues( array $values = null, $sanitize = false, array $items = null )
	{
		$this->clearValues();
		
		if( $values !== null ) {
			$this->mergeInnerValues( $values, $sanitize, $items );
		}
	}
	
	/**
	 * Merges given data with existing object data
	 *
	 * @param array $values   data to set
	 * @param bool  $sanitize if true, data will be sanitized
	 * @param array $items    list of items from $values list, that should be stored
	 */
	public function mergeInnerValues( array $values, $sanitize = false, array $items = null )
	{
		if( $items === null ) {
			$items = array_keys( $values );
		}
		
		foreach( $items as $name ) {
			if( isset( static::$items[ $name ] ) === false ) {
				throw new \Exception( "Invalid item '{$name}'" );
			}
			
			if( isset( $values[ $name ] ) ) {
				$value = $values[ $name ];
			} else {
				$value = null;
			}
			
			if( $sanitize ) {
				$value = self::sanitize( $name, $value );
			}
			
			$this->data[ $name ] = $value;
		}
	}
	
	/**
	 * Sanitizes given value
	 *
	 * @param  string $name  item name
	 * @param  mixed  $value value to sanitize
	 * @return mixed         sanitized value
	 */
	protected static function sanitize( $name, $value )
	{
		$item = static::$items[ $name ];
		
		// TODO
		if( $name === 'ID' && $value == null ) {
			return null;
		}
		
		if( $item['type'] !== 'string' && $value === '' ) {
			$value = null;
			
		} else {
			switch( $item['type'] ) {
				case 'bool':
				case 'boolean':
					$value = $value && $value !== 'f' && $value !== 'false';
					break;
					
				case 'int':
				case 'integer':
					$value = intval( $value );
					break;
					
				case 'float':
					$value = str_replace( ',', '.', $value );
					$value = floatval( $value );
					break;
					
				case 'string':
				default:
					$value = strval( $value );
					break;
			}
		}
		
		if( isset( $item['sanitize'] ) ) {
			$sanitize = $item['sanitize'];
			
			if( is_array( $sanitize ) === false ) {
				$sanitize = array( $sanitize );
			}
			
			foreach( $sanitize as $sItem ) {
				switch( $sItem ) {
					case 'nullOnEmpty':
						if( $value === '' ) {
							$value = null;
						}
						break;
						
					case 'foreingKey':
						if( $value == null ) {
							$value = null;
						}
						break;
				}
			}
		}
		
		return $value;
	}
	
	/**
	 * Returns list of all values indexed by database column names
	 *
	 * @return array
	 */
	public function dbGetValues()
	{
		$values = array();
		foreach( $this->data as $name => $value ) {
			$column = static::$items[ $name ]['dbColumn'];
			
			$values[ $column ] = $value;
		}
		
		return $values;
	}
	
	/**
	 * Sets all values using database column names as item identificators
	 *
	 * @param $values array
	 */
	public function dbSetValues( array $values )
	{
		foreach( static::$items as $name => $meta ) {
			if( isset( $meta['dbColumn'] ) === false ) {
				continue;
			}
			
			$column = $meta['dbColumn'];
			
			if( array_key_exists( $column, $values ) ) {
				$this->data[ $name ] = $values[ $column ];
			}
		}
	}
	
	/**
	 * Returns database table name
	 *
	 * @return string
	 */
	public static function dbGetTableName()
	{
		return static::$meta['tableName'];
	}
	
	/**
	 * Indicates whether data object is already persisted in database
	 *
	 * @return bool
	 */
	public function isPersisted()
	{
		return $this->get( 'ID' ) > 0;
	}
	
	/**
	 * Indicates whether data object has given property
	 *
	 * @param  string $name
	 * @return bool
	 */
	public static function hasProperty( $name )
	{
		return isset( static::$items[ $name ] );
	}
	
	/**
	 * Returns data object configuration
	 *
	 * @return array
	 */
	public static function getConfiguration()
	{
		return static::$items;
	}
	
/************* EXPLICIT GETTERS/SETTERS *************/
	
	/**
	 * ID getter
	 *
	 * @return int
	 */
	public function getID()
	{
		return $this->get( 'ID' );
	}
	
	/**
	 * ID setter
	 *
	 * @param int $value
	 */
	public function setID( $value )
	{
		$this->set( 'ID', $value );
	}
}