<?php
namespace WebBuilder\WebBuilder;

class InvalidBuilderTypeException extends WebBuilderException
{
	/**
	 * Constructor
	 *
	 * @param string $type supplied builder type
	 */
	public function __construct( $type )
	{
		parent::__construct( "Invalid blocks builder type '{$type}' supplied" );
	}
}
