<?php
namespace WebBuilder\Blocks\MainPage;

use WebBuilder\WebBlock;

class Menu extends WebBlock
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
			'leaf' => 'object'
		);
	}

	/**
	 * Tells which data block provides to nested blocks
	 *
	 * This is dummy implementation for blocks which does not
	 * provide any data.
	 *
	 * @return array|null
	 */
	public static function provides()
	{
		return null;
	}
}