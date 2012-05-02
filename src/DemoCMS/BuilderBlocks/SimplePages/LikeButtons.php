<?php
namespace DemoCMS\BuilderBlocks\SimplePages;

use WebBuilder\WebBlock;

class LikeButtons extends WebBlock
{
	public static function requires()
	{
		return array(
			'webPage' => 'cWebPage',
		);
	}
}