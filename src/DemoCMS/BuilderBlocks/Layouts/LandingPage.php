<?php
namespace DemoCMS\BuilderBlocks\Layouts;

use WebBuilder\WebBlock;

class LandingPage extends WebBlock
{
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
		return array(
			'webPage' => 'cWebPage'
		);
	}
}