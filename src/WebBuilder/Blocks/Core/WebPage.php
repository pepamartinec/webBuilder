<?php
namespace WebBuilder\Blocks\Core;

use WebBuilder\WebBuilder\WebBlock;

class WebPage extends WebBlock
{
	public static function requires()
	{
		return array(
			'structureItem' => 'cWebStructureItem'
		);
	}

	public static function provides()
	{
		return array(
			'pageObject' => 'iDataObject'
		);
	}
}