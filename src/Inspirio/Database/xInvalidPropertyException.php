<?php
namespace Inspirio\Database;

class xInvalidPropertyException extends xDatabaseException
{
	public function __construct( $dataObject, $property )
	{
		if( is_object( $dataObject ) ) {
			$dataObject = get_class( $dataObject );
		}
		
		parent::__construct( "{$dataObject} has no property called '{$property}'" );
	}
}