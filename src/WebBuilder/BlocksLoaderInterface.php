<?php
namespace WebBuilder;

use WebBuilder\DataObjects\BlockSet;

interface BlocksLoaderInterface
{
	/**
	 * Return complete blocks structure for given blocks set
	 *
	 * @param  BlockSet $blockSet desired blocks set
	 * @return array array[ WebPageInterface ]
	 */
	public function fetchBlocksInstances( BlockSet $blockSet );
}