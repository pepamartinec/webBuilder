<?php
namespace Inspirio\BuilderBlocks\Pages;

use Inspirio\Database\cDBFeederBase;
use WebBuilder\WebBlock;

class ItemLoader extends WebBlock
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
			'page' => 'cPage'
		);
	}
	
	public function setupData( $pageID )
	{
		$pagesFeeder = new cDBFeederBase( '\\Inspirio\\cPage', $this->database );
		$page = $pagesFeeder->whereID( $pageID )->getOne();
		
		return array(
			'page' => $page
		);
	}
}