<?php
namespace WebBuilder\Blocks\MainPage;

use WebBuilder\WebBlock;

class TwoDependencies extends WebBlock
{
	public static function requires()
	{
		return array(
			'sectionID' => 'int',
			'threadID'  => 'int'
		);
	}

	public static function provides()
	{
		return array(
			'info' => 'string'
		);
	}

	public function setupData( $categoryID )
	{
		return array(
			'info' => 'OK'
		);
	}
}