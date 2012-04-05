<?php
namespace WebBuilder\WebBuilder;

use Inspirio\Database\cDatabase;

/**
 * Web block interface
 *
 * Interface, that every WebBlock is requested to implement
 *
 * @author Josef Martinec
 */
interface WebBlockInterface
{
	/**
	 * Constructor
	 *
	 * @param \Database $database
	 */
	public function __construct( cDatabase $database );

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
	public static function requires();

	/**
	 * Tells which data block provides to nested blocks
	 *
	 * This is dummy implementation for blocks which does not
	 * provide any data.
	 *
	 * @return array|null
	 */
	public static function provides();

	/**
	 * Tells which data block provides to nested blocks,
	 * takes data provided by parent blocks in account
	 *
	 * @param  array      $context
	 * @return array|null
	 */
	public static function contextedProvides( array $context );

	/**
	 * Block wireframe definition
	 *
	 * Wireframe is used for rendering block in templates administration canvas.
	 *
	 * @return string|null
	 */
	public static function wireframe();
}