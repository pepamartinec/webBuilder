<?php
namespace WebBuilder;

use Inspirio\Database\cDatabase;

/**
 * WebBlocksFactory for WebBlock blocks
 */
class WebBlocksFactory implements WebBlocksFactoryInterface
{
	/**
	 * @var \Inspirio\Database\cDatabase
	 */
	protected $database;

	public function __construct( cDatabase $database )
	{
		$this->database = $database;
	}

	/**
	 * Creates new block object
	 *
	 * @param  string $blockObjectName
	 * @return WebBlock
	 */
	public function createBlock( $blockObjectName )
	{
		return new $blockObjectName( $this->database );
	}
}