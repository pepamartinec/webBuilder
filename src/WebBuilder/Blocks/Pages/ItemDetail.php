<?php
namespace WebBuilder\Blocks\Pages;

use Inspirio\Database\cDBFeederBase;

use WebBuilder\WebBuilder\WebBlock;

class ItemDetail extends WebBlock
{
	public static function requires()
	{
		return array(
			'page' => 'cPage'
		);
	}
}