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
		$webPages      = $webPageFeeder->whereColumnEq( 'parent_ID', $webPage->getID() )
		                               ->whereColumnEq( 'published', true )
		                               ->indexBy( 'ID' )
		                               ->get();

		if( $webPages ) {
			// load content pages
			$simplePageFeeder = new cDBFeederBase( '\\DemoCMS\\cSimplePage', $this->database );
			$simplePages      = $simplePageFeeder->whereColumnIn( 'web_page_ID', array_keys( $webPages ) )
			                                     ->indexBy( 'web_page_ID' )
			                                     ->get();

			// load title images
			$titleImageIDs = array();
			foreach( $simplePages as $simplePage ) {
				$titleImageID = $simplePage->getTitleImageID();

				if( $titleImageID ) {
					$titleImageIDs[] = $titleImageID;
				}
			}

			if( sizeof( $titleImageIDs ) > 0 ) {
				$imageHandler = new cImageHandler( $this->database );
				$imageFeeder  = $imageHandler->getImageFeeder();
				$titleImages  = $imageFeeder->whereColumnIn( 'ID', $titleImageIDs )
				                            ->indexBy( 'ID' )
				                            ->get();

			} else {
				$titleImages = array();
			}

			$validWebPages = array();

			// attach to the web pages
			foreach( $webPages as $webPageID => $webPage ) {
				if( ! isset( $simplePages[ $webPageID ] ) ) {
					continue;
				}

				$simplePage = $simplePages[ $webPageID ];

				$simplePage->setWebPage( $webPage );
				$webPage->setContentItem( $simplePage );

				$titleImageID = $simplePage->getTitleImageID();
				if( $titleImageID && isset( $titleImages[ $titleImageID ] ) ) {
					$simplePage->setTitleImage( $titleImages[ $titleImageID ] );
				}

				$validWebPages[] = $webPage;
			}

			$webPages = $validWebPages;
		}

		return array(
			'pages' => $webPages
		);
	}
}