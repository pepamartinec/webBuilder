<?php
namespace DemoCMS\BuilderBlocks\SimplePages;

use DemoCMS\cImageHandler;

use DemoCMS\cWebPage;
use Inspirio\Database\cDBFeederBase;
use WebBuilder\WebBlock;

class PageList extends WebBlock
{
	public static function requires()
	{
		return array(
			'parent' => 'cWebPage'
		);
	}

	public static function provides()
	{
		return array(
			'pages' => 'array[ cWebPage ]',
			'page'  => '{pages}'
		);
	}

	public function setupData( cWebPage $webPage )
	{
		$webPageFeeder = new cDBFeederBase( '\\DemoCMS\\cWebPage', $this->database );
		$webPages      = $webPageFeeder->whereColumnEq( 'parent_ID', $webPage->getID() )->indexBy( 'ID' )->get();

		if( $webPages ) {
			// load title images
			$titleImageIDs = array();
			foreach( $webPages as $webPage ) {
				$titleImageID = $webPage->getTitleImageID();

				if( $titleImageID ) {
					$titleImageIDs[] = $titleImageID;
				}
			}

			if( sizeof( $titleImageIDs ) > 0 ) {
				$imageHandler = new cImageHandler( $this->database );
				$imageFeeder  = $imageHandler->getImageFeeder();
				$titleImages  = $imageFeeder->whereColumnIn( 'ID', $titleImageIDs )->get();

			} else {
				$titleImages = null;
			}

			// load content pages
			$simplePageFeeder = new cDBFeederBase( '\\DemoCMS\\cSimplePage', $this->database );
			$simplePages      = $simplePageFeeder->whereColumnIn( 'web_page_ID', array_keys( $webPages ) )->indexBy( 'web_page_ID' )->get();

			// attach to the webb pages
			foreach( $webPages as $webPageID => $webPage ) {
				if( isset( $simplePages[ $webPageID ] ) ) {
					$simplePages[ $webPageID ]->setWebPage( $webPage );
					$webPage->setContentItem( $simplePages[ $webPageID ] );
				}

				$titleImageID = $webPage->getTitleImageID();

				if( $titleImageID && isset( $titleImages[ $titleImageID ] ) ) {
					$webPage->setTitleImage( $titleImages[ $titleImageID ] );
				}
			}
		}

		return array(
			'pages' => $webPages
		);
	}
}