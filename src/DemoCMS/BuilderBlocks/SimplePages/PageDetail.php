<?php
namespace DemoCMS\BuilderBlocks\SimplePages;

use WebBuilder\WebBlock;

class PageDetail extends WebBlock
{
	public static function requires()
	{
		return array(
			'page' => 'cWebPage'
		);
	}
}