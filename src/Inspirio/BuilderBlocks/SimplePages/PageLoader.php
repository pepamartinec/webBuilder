<?php
namespace Inspirio\BuilderBlocks\SimplePages;

use Inspirio\Database\cDBFeederBase;
use WebBuilder\WebBlock;

class PageLoader extends WebBlock
{
	public static function requires()
	{
		return array(
			'pageID' => 'int'
		);
	}

	public static function provides()
	{
		return array(
			'page' => 'cSimplePage'
		);
	}

	public function setupData( $pageID )
	{
		$pageFeeder = new cDBFeederBase( '\\Inspirio\\cSimplePage', $this->database );
		$page       = $pagesFeeder->whereID( $pageID )->getOne();

		return array(
			'page' => $page
		);
	}
}