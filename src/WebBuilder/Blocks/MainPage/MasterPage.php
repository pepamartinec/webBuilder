<?php
namespace WebBuilder\Blocks\MainPage;

use WebBuilder\WebPageInterface;
use WebBuilder\WebBlock;

class MasterPage extends WebBlock
{
	public static function requires()
	{
		return array(
			'structureItem' => 'WebPageInterface'
		);
	}

	public static function provides()
	{
		return array(
			'leaf' => 'object'
		);
	}

	public function setupData( WebPageInterface $webPage )
	{
		$entity = null;

		if( $webPage->getEntityType() && $webPage->getEntityID() ) {
			$entityType = $webPage->getEntityType();
			$entityID   = $webPage->getEntityID();

			$entitiesFeeder = new \cDBFeederBase( $entityType, $this->database );
			$entity = $entitiesFeeder->whereID( $entityID )->getOne();

			if( $entity === null ) {
				throw new Exception( 'Given struture item has not valid entity ID' );
			}
		}

		return array(
			'leaf' => $entity
		);
	}
}