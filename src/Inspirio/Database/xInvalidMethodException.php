<?php
namespace Inspirio\Database;

class xInvalidMethodException extends xDataObjectException
{
	public function __construct( aDataObject $dataObject, $method )
	{
		$class = get_class( $dataObject );

		parent::__construct( "{$class} has no method called '{$method}'" );
	}
}