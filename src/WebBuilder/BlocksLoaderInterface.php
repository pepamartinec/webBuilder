<?php
namespace WebBuilder;

use WebBuilder\DataObjects\BlocksSet;

interface BlocksLoaderInterface
{
	/**
	 * Return complete blocks structure for given blocks set
	 *
	 * @param  BlocksSet $blocksSet desired blocks set
	 * @return array array[ WebPageInterface ]
	 */
	public function fetchBlocksInstances( BlocksSet $blocksSet );
}