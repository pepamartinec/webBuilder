<?php
namespace DemoCMS\BuilderBlocks\Navigation;

use DemoCMS\cWebPage;

use WebBuilder\WebPageInterface;
use Inspirio\Database\cDBFeederBase;

use WebBuilder\WebBlock;

class Menu extends WebBlock
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
			'menuItems' => 'array[ cWebPage ]'
		);
	}

	public function setupData( cWebPage $webPage )
	{
		$webPageFeeder = new cDBFeederBase( '\\DemoCMS\\cWebPage', $this->database );
		$webPages      = $webPageFeeder->whereColumnEq( 'published', true )
		                               ->where( 'valid_from IS NULL OR valid_from <= NOW()' )
		                               ->where( 'valid_to IS NULL   OR valid_to   >= NOW()' )
		                               ->orderBy( 'position', 'asc' )
		                               ->indexBy( 'ID' )
		                               ->get();

		$roots    = array();
		$itemsBag = array();

		foreach( $webPages as $webPage ) {
			/* @var $webPage cWebPage */
			$itemID   = $webPage->getID();
			$parentID = $webPage->getParentID();

			if( $parentID == null ) {
				$roots[] = $webPage;

			} else {
				if( isset( $itemsBag[ $parentID ] ) === false ) {
					$itemsBag[ $parentID ] = array();
				}

				$itemsBag[ $parentID ][] = $webPage;
			}
		}

		foreach( $itemsBag as $itemID => $subItems ) {
			$webPages[ $itemID ]->setChildren( $subItems );
		}

		return array(
			'menuItems' => $roots[0]->getChildren()
		);
	}
}
