<?php
namespace DemoCMS\Administration\WebEditor;

use ExtAdmin\RequestInterface;
use ExtAdmin\Response\ActionResponse;
use DemoCMS\Administration\WebEditor\AbstractPageEditor;
use Inspirio\Database\cDBFeederBase;
use DemoCMS\cWebPage;
use DemoCMS\cSimplePage;

class SimplePageEditor extends AbstractPageEditor
{
	protected function editorClass()
	{
		return 'DemoCMS.module.WebEditor.SimplePageEditor';
	}

	protected function loadAssociatedData( cWebPage $webPage )
	{
		$simplePageFeeder = new cDBFeederBase( '\\DemoCMS\\cSimplePage', $this->database );
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
		$simplePageFeeder = new cDBFeederBase( '\\DemoCMS\\cSimplePage', $this->database );
		$simplePage       = $simplePageFeeder->whereColumnEq( 'web_page_ID', $webPage->getID() )->getOne();

		if( $simplePage == null ) {
			$simplePage = new cSimplePage();
		}

		$simplePage->mergeInnerValues( array(
			'webPageID'    => $webPage->getID(),
			'perex'        => $request->getData( 'perex', 'string' ),
			'content'      => $request->getData( 'content', 'string' ),
			'titleImageID' => $request->getData( 'titleImageID', 'int' ),
		), true );

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

	/**
	 * Adds associated data for page preview
	 *
	 * @param RequestInterface $request
	 * @param cWebPage $webPage
	 */
	protected function associatePreviewData( RequestInterface $request, cWebPage $webPage )
	{
		$simplePage = new cSimplePage( array(
			'webPageID'    => $webPage->getID(),
			'perex'        => $request->getData( 'perex', 'string' ),
			'content'      => $request->getData( 'content', 'string' ),
			'titleImageID' => $request->getData( 'titleImageID', 'int' ),
		) );

		$simplePage->setWebPage( $webPage );
		$webPage->setContentItem( $simplePage );
	}

}
