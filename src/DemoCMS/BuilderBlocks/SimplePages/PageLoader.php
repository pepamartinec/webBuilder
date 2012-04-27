<?php
namespace DemoCMS\BuilderBlocks\SimplePages;

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
			'page' => 'cWebPage'
		);
	}

	public function setupData( $pageID )
	{
		$webPageFeeder = new cDBFeederBase( '\\DemoCMS\\cWebPage', $this->database );
		$webPage       = $webPageFeeder->whereID( $pageID )->getOne();

		$simplePageFeeder = new cDBFeederBase( '\\DemoCMS\\cSimplePage', $this->database );
		$simplePage       = $simplePageFeeder->whereColumnEq( 'web_page_ID', $pageID )->getOne();

		$webPage->setContentItem( $simplePage );
		$simplePage->setWebPage( $webPage );

		return array(
			'page' => $webPage
		);
	}
}