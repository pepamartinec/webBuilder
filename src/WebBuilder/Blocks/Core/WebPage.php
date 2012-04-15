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

	public static function config()
	{
		return array(
			'stylesheet' => array(
				'type'      => 'combo',
				'default'   => 'public/css/style.css',
				'allowNull' => false,
				'items'     => array(
					'public/css/style.css' => 'Standard'
				),
			)
		);
	}
}