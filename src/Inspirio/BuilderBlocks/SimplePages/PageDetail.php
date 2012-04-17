<?php
namespace Inspirio\BuilderBlocks\SimplePages;

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