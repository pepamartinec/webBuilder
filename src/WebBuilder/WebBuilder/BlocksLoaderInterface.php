<?php
namespace WebBuilder\WebBuilder;

use WebBuilder\WebBuilder\DataObjects\BlocksSet;

interface BlocksLoaderInterface
{
	/**
	 * Return complete blocks structure for given blocks set
	 *
	 * @param  BlocksSet $blocksSet desired blocks set
	 * @return array                 array( WebStructureItem )
	 */
	public function fetchBlocksInstances( BlocksSet $blocksSet );
}