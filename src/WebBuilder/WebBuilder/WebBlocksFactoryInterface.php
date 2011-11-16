<?php
namespace WebBuilder\WebBuilder;

interface WebBlocksFactoryInterface
{
	/**
	 * Creates new block object
	 *
	 * @param  string $blockObjectName
	 * @return WebBlockInterface
	 */
	public function createBlock( $blockObjectName );
}