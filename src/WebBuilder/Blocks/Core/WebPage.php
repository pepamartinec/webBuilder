<?php
namespace WebBuilder\Blocks\Core;

use WebBuilder\WebBlock;

class WebPage extends WebBlock
{
	public static function requires()
	{
		return array(
			'webPage' => 'WebPageInterface'
		);
	}
}