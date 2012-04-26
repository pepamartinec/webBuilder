<?php
namespace Inspirio\Administration\WebEditor;

use ExtAdmin\RequestInterface;
use ExtAdmin\Response\ActionResponse;
use Inspirio\Administration\WebEditor\AbstractPageEditor;
use Inspirio\Database\cDBFeederBase;
use Inspirio\cWebPage;
use Inspirio\cSimplePage;

class SimplePageEditor extends AbstractPageEditor
{
	protected function editorClass()
	{
		return 'Inspirio.module.WebEditor.SimplePageEditor';
	}

	protected function loadAssociatedData( cWebPage $webPage )
	{
		$simplePageFeeder = new cDBFeederBase( '\\Inspirio\\cSimplePage', $this->database );
		$simplePage       = $simplePageFeeder->whereColumnEq( 'web_page_ID', $webPage->getID() )->getOne();

		if( $simplePage ) {
			return array(
				'perex'        => $simplePage->getPerex(),
				'content'      => $simplePage->getContent(),
				'titleImageID' => $simplePage->getTitleImageID(),
			);

		} else {
			return array();
		}
	}

	protected function saveAssociatedData( RequestInterface $request, cWebPage $webPage )
	{
		$simplePageFeeder = new cDBFeederBase( '\\Inspirio\\cSimplePage', $this->database );
		$simplePage       = $simplePageFeeder->whereColumnEq( 'web_page_ID', $webPage->getID() )->getOne();

		if( $simplePage == null ) {
			$simplePage = new cSimplePage();
		}

		$simplePage->mergeInnerValues( array(
			'webPageID'    => $webPage->getID(),
			'perex'        => $request->getData( 'perex', 'string' ),
			'content'      => $request->getData( 'content', 'string' ),
			'titleImageID' => $request->getData( 'titleImageID', 'int' ),
		) );

		$simplePageFeeder->save( $simplePage );

		$response = new ActionResponse( true );
		$response->setData( array(
			'perex'        => $simplePage->getPerex(),
			'content'      => $simplePage->getContent(),
			'titleImageID' => $simplePage->getTitleImageID(),
		));

		return $response;
	}

	protected function deleteAssociatedData( RequestInterface $request, array $webPages )
	{
		return new ActionResponse( true );
	}

}
