<?php
namespace Inspirio\BuilderBlocks\Pages;

use Inspirio\Database\cDBFeederBase;

use WebBuilder\WebBlock;

class ItemDetail extends WebBlock
{
	public static function requires()
	{
		return array(
			'page' => 'cPage'
		);
	}
}