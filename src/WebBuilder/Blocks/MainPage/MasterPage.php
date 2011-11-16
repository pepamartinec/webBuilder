<?php
namespace WebBuilder\Blocks\MainPage;

use WebBuilder\WebBuilder\DataObjects\WebStructureItem;

use WebBuilder\WebBuilder\WebBlock;

class MasterPage extends WebBlock
{
	public static function requires()
	{
		return array(
			'structureItem' => 'dWebStructureItem'
		);
	}

	public static function provides()
	{
		return array(
			'leaf' => 'object'
		);
	}

	public function setupData( WebStructureItem $structureItem )
	{
		$entity = null;

		if( $structureItem->getEntityType() && $structureItem->getEntityID() ) {
			$entityType = $structureItem->getEntityType();
			$entityID   = $structureItem->getEntityID();
			
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