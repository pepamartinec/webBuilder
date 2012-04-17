<?php
namespace Inspirio\BuilderBlocks\SimplePages;

use Inspirio\Database\cDBFeederBase;

use WebBuilder\WebBlock;

class PageOverview extends WebBlock
{
	public static function requires()
	{
		return array(
			'page' => 'cWebPage'
		);
	}
}