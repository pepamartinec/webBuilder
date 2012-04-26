<?php
namespace Inspirio\BuilderBlocks\SimplePages;

use Inspirio\Database\cDBFeederBase;
use WebBuilder\WebBlock;

class PageList extends WebBlock
{
	public static function requires()
	{
		return array(
			'parentID' => 'int'
		);
	}

	public static function provides()
	{
		return array(
			'pages' => 'array[ cWebPage ]',
			'page'  => '{pages}'
		);
	}

	public function setupData( $parentID )
	{
		$simplePages = null;

		$webPageFeeder = new cDBFeederBase( '\\Inspirio\\cWebPage', $this->database );
		$webPages      = $webPageFeeder->whereColumnEq( 'parent_ID', $parentID )->indexBy( 'ID' )->get();

		if( $webPages ) {
			$simplePageFeeder = new cDBFeederBase( '\\Inspirio\\cSimplePage', $this->database );
			$simplePages      = $simplePageFeeder->whereColumnIn( 'web_page_ID', array_keys( $webPages ) )->get();

			if( $simplePages ) {
				foreach( $simplePages as $simplePage ) {
					$webPage = $webPages[ $simplePage->getWebPageID() ];

					$simplePage->setWebPage( $webPage );
					$webPage->setContentItem( $simplePage );
				}
			}
		}

		return array(
			'pages' => $webPages
		);
	}
}