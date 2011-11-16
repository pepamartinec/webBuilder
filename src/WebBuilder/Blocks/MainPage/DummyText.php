<?php
namespace WebBuilder\Blocks\MainPage;

use WebBuilder\WebBuilder\WebBlock;

class DummyText extends WebBlock
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
		return null;
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