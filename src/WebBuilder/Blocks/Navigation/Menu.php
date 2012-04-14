<?php
namespace WebBuilder\Blocks\Navigation;

use WebBuilder\WebPageInterface;
use Inspirio\Database\cDBFeederBase;

use WebBuilder\WebBlock;

class Menu extends WebBlock
{
	public static function requires()
	{
		return array(
			'structureItem' => 'WebPage'
		);
	}

	public static function provides()
	{
		return array(
			'menuItems' => 'array[ WebPage ]'
		);
	}

	public function setupData( WebPageInterface $webPage )
	{
		$itemsFeeder = new cDBFeederBase( '\WebBuilder\DataObjects\WebPage', $this->database );
		$items       = $itemsFeeder->indexBy( 'ID' )->get();

		$roots    = array();
		$itemsBag = array();

		foreach( $items as $item ) {
			/* @var $item WebPage */
			$itemID   = $item->getID();
			$parentID = $item->getParentID();

			if( $parentID == null ) {
				$roots[] = $item;

			} else {
				if( isset( $itemsBag[ $parentID ] ) === false ) {
					$itemsBag[ $parentID ] = array();
				}

				$itemsBag[ $parentID ][] = $item;
			}
		}

		foreach( $itemsBag as $itemID => $subItems ) {
			$items[ $itemID ]->setDescendants( $subItems );
		}

		return array(
			'menuItems' => $roots[0]->getDescendants()
		);
	}
}
