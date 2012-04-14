<?php
namespace WebBuilder;

use WebBuilder\DataObjects\WebPage;

interface WebBuilderInterface
{
	/**
	 * Renders page for given web structure item
	 *
	 * @param  WebPage $sItem
	 * @return string
	 *
	 * @throws WebBuilder\BlocksSetIntegrityException
	 */
	public function render( WebPage $structureItem );
}