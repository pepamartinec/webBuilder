<?php
namespace WebBuilder;

use WebBuilder\DataObjects\WebStructureItem;

interface WebBuilderInterface
{
	/**
	 * Renders page for given web structure item
	 *
	 * @param  WebStructureItem $sItem
	 * @return string
	 *
	 * @throws WebBuilder\BlocksSetIntegrityException
	 */
	public function render( WebStructureItem $structureItem );
}