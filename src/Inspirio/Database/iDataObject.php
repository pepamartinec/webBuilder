<?php
namespace Inspirio\Database;

interface iDataObject
{
	/**
	 * Returns data object configuration
	 *
	 * @return array
	 */
	public static function getConfiguration();
	
	/**
	 * Indicates whether data object is already persisted in database
	 *
	 * @return bool
	 */
	public function isPersisted();
	
	/**
	 * Indicates whether data object has given property
	 *
	 * @param  string $name
	 * @return bool
	 */
	public static function hasProperty( $name );
	
	/**
	 * Universal property values setter
	 *
	 * @param string $property
	 * @param mixed  $value
	 * @param bool   $sanitize
	 *
	 * @throws xInvalidPropertyException
	 */
	public function set( $property, $value, $sanitize = false );
	
	/**
	 * Universal property getter
	 *
	 * @param  string $property
	 * @return mixed
	 *
	 * @throws xInvalidPropertyException
	 */
	public function get( $property );
	
	/**
	 * Universal property value existance checker
	 *
	 * Returns TRUE when property has value (even NULL)
	 *
	 * @param  string $property
	 * @return bool
	 */
	public function has( $property );
	
	/**
	 * Clears all values in object
	 *
	 */
	public function clearValues();
	
	/**
	 * Returns all object values
	 *
	 * @return array
	 */
	public function getInnerValues();
	
	/**
	 * Replaces object data
	 *
	 * @param array|null $values   data to set
	 * @param bool       $sanitize if true, data will be sanitized
	 * @param array      $items    list of items from $values list, that should be stored
	 */
	public function setInnerValues( array $values = null, $sanitize = false, array $items = null );
	
	/**
	 * Merges given data with existing object data
	 *
	 * @param array $values   data to set
	 * @param bool  $sanitize if true, data will be sanitized
	 * @param array $items    list of items from $values list, that should be stored
	 */
	public function mergeInnerValues( array $values, $sanitize = false, array $items = null );
		
/************* EXPLICIT GETTERS/SETTERS *************/
	
	/**
	 * ID getter
	 *
	 * @return int
	 */
	public function getID();
	
	/**
	 * ID setter
	 *
	 * @param int $value
	 */
	public function setID( $value );
}