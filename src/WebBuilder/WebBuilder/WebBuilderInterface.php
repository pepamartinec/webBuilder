<?php
namespace WebBuilder\WebBuilder;

use WebBuilder\WebBuilder\DataObjects\WebStructureItem;

interface WebBuilderInterface
{
	/**
	 * Renders page for given web structure item
	 *
	 * @param  WebStructureItem $sItem
	 * @return string
	 *
	 * @throws WebBuilder\WebBuilder\BlocksSetIntegrityException
	 */
	public function render( WebStructureItem $structureItem );
}