<?php
namespace WebBuilder\WebBuilder;

use Inspirio\Database\cDatabase;

/**
 * Web block prototype
 *
 * Should be used as template for implementation
 * of custom web blocks
 *
 * @author Josef Martinec
 */
abstract class WebBlock implements WebBlockInterface
{
	/**
	 * @var \Database
	 */
	protected $database;

	/**
	 * Constructor
	 *
	 * @param \Database $database
	 */
	public final function __construct( cDatabase $database )
	{
		$this->database = $database;
	}

	/**
	 * Setups data
	 *
	 */
//	public abstract function setup();

	/**
	 * Tells which data block requires from parent block
	 *
	 * This is dummy implementation for blocks which does not
	 * require any data.
	 *
	 * @return array|null
	 */
	public static function requires()
	{
		return null;
	}

	/**
	 * Tells which data block provides to nested blocks
	 *
	 * This is dummy implementation for blocks which does not
	 * provide any data.
	 *
	 * @return array|null
	 */
	public static function provides()
	{
		return null;
	}

	/**
	 * Tells which data block provides to nested blocks,
	 * takes data provided by parent blocks in account
	 *
	 * @param  array      $context
	 * @return array|null
	 */
	public static final function contextedProvides( array $context )
	{
		static $data = null;

		if( $data === null ) {
			$data = WebBuilder_Grammar::precompile( self::provides(), $context );
		}

		return $data;
	}
}