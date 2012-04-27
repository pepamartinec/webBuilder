<?php
namespace DemoCMS\BuilderBlocks\Navigation;

use DemoCMS\cWebPage;
use Inspirio\Database\cDBFeederBase;
use WebBuilder\WebBlock;

class Breadcrumbs extends WebBlock
{
	public static function requires()
	{
		return array(
			'webPage' => 'cWebPage'
		);
	}

	public static function provides()
	{
		return array(
			'pagesPath' => 'array[ cWebPage ]'
		);
	}

	public function setupData( cWebPage $webPage )
	{
		$webPageFeeder = new cDBFeederBase( '\\DemoCMS\\cWebPage', $this->database );
		$pages         = array( $webPage );

		while( $webPage->getParentID() ) {
			$webPage = $webPageFeeder->whereID( $webPage->getParentID() )->getOne();

			if( $webPage == null ) {
				break;
			}

			array_unshift( $pages, $webPage );
		}

		return array(
			'pagesPath' => $pages
		);
	}
}
