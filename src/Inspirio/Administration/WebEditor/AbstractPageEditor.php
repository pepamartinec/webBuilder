<?php
namespace Inspirio\Administration\WebEditor;

use Inspirio\cImageHandler;

use ExtAdmin\Request\AbstractRequest;

use WebBuilder\Persistance\DatabaseUpdater;

use WebBuilder\DataDependencies\Solver;
use WebBuilder\Persistance\DatabaseLoader;
use Inspirio\Database\cDBFeederBase;
use WebBuilder\DataObjects\BlockSet;
use ExtAdmin\Module\DataEditor\DataEditor;
use ExtAdmin\RequestInterface;
use ExtAdmin\Response\ActionResponse;
use Inspirio\Database\cDatabase;
use Inspirio\cWebPage;

/**
 * Base PageEditor class
 *
 */
abstract class AbstractPageEditor extends DataEditor
{
	/**
	 * @var cDatabase
	 */
	protected $database;

	/**
	 * @var \SimpleXMLElement
	 */
	protected $labels;

	/**
	 * Module constructor
	 *
	 * @param cDatabase $database
	 * @param \SimpleXMLElement $labels
	 */
	public function __construct( cDatabase $database, \SimpleXMLElement $labels )
	{
		$this->database = $database;
		$this->labels   = $labels;
	}

	/**
	 * Returns module actions definition
	 *
	 * Used for defining actions within concrete modules implementations
	 *
	 * @return array
	 */
	protected function actions()
	{
		return array(
			'loadData_new'    => true,
			'loadData_record' => true,

			'saveData' => array(
				'type' => 'save'
			),

			'cancel' => array(
				'type' => 'cancel'
			),
		);
	}

	/**
	 * Returns the editor class name
	 *
	 * @return string
	 */
	protected abstract function editorClass();

	/**
	 * Module view configuration
	 *
	 * @return array
	 */
	public function viewConfiguration()
	{
		return array(
			'type'       => $this->editorClass(),
			'loadAction' => 'loadData_record',
			'saveAction' => 'saveData',

			'buttons' => array(
				array(
					'type'   => 'button',
					'text'   => 'Uložit',
					'action' => 'saveData'
				),

				array(
					'type'   => 'button',
					'text'   => 'Storno',
					'action' => 'cancel'
				)
			)
		);
	}

	/**
	 * Loads the editor data of an existing record
	 *
	 * @param RequestInterface $request
	 * @return ActionResponse
	 */
	public function loadData_new( RequestInterface $request )
	{
		$response = new ActionResponse( true );
		$response->setData( array(
			'parentID' => $request->getData( 'ID', 'int' )
		) );

		return $response;
	}

	/**
	 * Loads associated editor data
	 *
	 * @param cWebPage $webPage
	 * @return array
	 */
	protected abstract function loadAssociatedData( cWebPage $webPage );

	/**
	 * Loads the editor data of an existing record
	 *
	 * @param RequestInterface $request
	 * @return ActionResponse
	 */
	public function loadData_record( RequestInterface $request )
	{
		$recordID = $request->getData( 'ID', 'int' );

		// load web page
		$webPageFeeder = new cDBFeederBase( '\\Inspirio\\cWebPage', $this->database );
		$webPage       = $webPageFeeder->whereID( $recordID )->getOne();

		$data = array(
			'ID'         => $webPage->getID(),
			'parentID'   => $webPage->getParentID(),
			'blockSetID' => $webPage->getBlockSetID(),
			'title'      => $webPage->getTitle(),
			'urlName'    => $webPage->getUrlName(),
			'published'  => $webPage->getPublished(),
			'validFrom'  => $webPage->getValidFrom(),
			'validTo'    => $webPage->getValidTo(),
		);

		// load discussion posts
		$postFeeder = new cDBFeederBase( '\\Inspirio\\cDiscussionPost', $this->database );
		$posts      = $postFeeder->whereColumnEq( 'web_page_ID', $webPage->getID() )->orderBy( 'created_on', 'desc' )->get();

		$postsData = array();

		if( $posts != null ) {
			foreach( $posts as $post ) {
				$postsData[] = array(
					'ID'          => $post->getID(),
					'authorName'  => $post->getAuthorName(),
					'authorEmail' => $post->getAuthorEmail(),
					'content'     => $post->getContent(),
				);
			}
		}

		$data['discussion'] = $postsData;

		// load template
		$blockSetID = $webPage->getBlockSetID();

		if( $blockSetID ) {
			$blockSetFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockSet', $this->database );
			$blockSet       = $blockSetFeeder->whereID( $webPage->getBlockSetID() )->getOne();

			$loader       = new DatabaseLoader( $blockSetFeeder );
			$instances    = $loader->fetchBlocksInstances( $blockSet );
			$rootInstance = reset( $instances );

			if( $blockSet ) {
				$data['parentBlockSetID'] = $blockSet->getParentID();
			}

			$data['template']         = $rootInstance->export();
		}

		// load associated data
		$data += $this->loadAssociatedData( $webPage );

		$response = new ActionResponse( true );
		$response->setData( $data );

		return $response;
	}

	/**
	 * Saves the associated data
	 *
	 * @param RequestInterface $request
	 * @param cWebPage $webPage
	 * @return ActionResponse
	 */
	protected abstract function saveAssociatedData( RequestInterface $request, cWebPage $webPage );

	/**
	 * Saves the data received from the editor
	 *
	 * @param RequestInterface $request
	 * @return ActionResponse
	 */
	public function saveData( RequestInterface $request )
	{
		try {
			$this->database->transactionStart();

			$title = $request->getData( 'title', 'string' );

			// save template
			$blockSet = new BlockSet( array(
				'ID'       => $request->getData( 'blockSetID', 'int' ),
				'parentID' => $request->getData( 'parentBlockSetID', 'int' ),
				'name'     => "[{$title}]",
			), true );

			$blockSetFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockSet', $this->database );
			$blockSetFeeder->save( $blockSet );

			$updater   = new DatabaseUpdater( $this->database );
			$instances = $updater->saveBlockInstances( $blockSet, $request->getRawData('template') );

			$rootInstance = reset( $instances );

			// save webPage
			$webPage = new cWebPage( array(
				'ID'         => $request->getData( 'ID', 'int' ),
				'parentID'   => $request->getData( 'parentID', 'int' ),
				'blockSetID' => $blockSet->getID(),
				'type'       => 'simplePage',
				'title'      => $title,
				'urlName'    => $request->getData( 'urlName', 'string' ),
				'published'  => $request->getData( 'published', 'bool' ),
				'validFrom'  => $request->getData( 'validFrom', 'string' ),
				'validTo'    => $request->getData( 'validTo',   'string' ),
			), true );

			$webPageFeeder = new cDBFeederBase( '\\Inspirio\\cWebPage', $this->database );
			$webPageFeeder->save( $webPage );

			// save discussion posts
			$postFeeder = new cDBFeederBase( '\\Inspirio\\cDiscussionPost', $this->database );
			$removedPostsRaw = $request->getRawData( 'discussion' );

			if( is_array( $removedPostsRaw ) ) {
				$removedPosts = array();

				foreach( $removedPostsRaw as $rawID ) {
					$ID = AbstractRequest::secureValue( $rawID, 'int' );

					if( $ID ) {
						$removedPosts[] = $ID;
					}
				}

				if( sizeof( $removedPosts ) > 0 ) {
					$postFeeder->whereColumnIn( 'ID', $removedPosts )->remove();
				}
			}

			$posts = $postFeeder->whereColumnEq( 'web_page_ID', $webPage->getID() )->orderBy( 'created_on', 'desc' )->get();
			$postsData = array();

			if( $posts != null ) {
				foreach( $posts as $post ) {
					$postsData[] = array(
						'ID'          => $post->getID(),
						'authorName'  => $post->getAuthorName(),
						'authorEmail' => $post->getAuthorEmail(),
						'content'     => $post->getContent()
					);
				}
			}

			// save simplePage
			$response = $this->saveAssociatedData( $request, $webPage );

			if( $response->getSuccess() ) {
				$this->database->transactionCommit();

			} else {
				$this->database->transactionRollback();
			}

		} catch( Exception $e ) {
			$this->database->transactionRollback();
			throw $e;
		}

		// add generic data to response (overriding conflicting keys)
		$response->setData( array(
			'ID'         => $webPage->getID(),
			'parentID'   => $webPage->getParentID(),
			'blockSetID' => $webPage->getBlockSetID(),
			'title'      => $webPage->getTitle(),
			'urlName'    => $webPage->getUrlName(),
			'published'  => $webPage->getPublished(),
			'validFrom'  => $webPage->getValidFrom(),
			'validTo'    => $webPage->getValidTo(),

			'template'   => $rootInstance->export(),
			'discussion' => $postsData,
		) + $response->getData() );

		return $response;
	}

	/**
	 * Deletes the associated data
	 *
	 * @param RequestInterface $request
	 * @param cWebPage $webPage
	 * @return ActionResponse
	 */
	protected abstract function deleteAssociatedData( RequestInterface $request, array $webPages );

	/**
	 * Deletes the records
	 *
	 * @param RequestInterface $request
	 * @return ActionResponse
	 */
	public function deleteData( RequestInterface $request )
	{
		$recordIDs = array();
		$records   = $request->getRawData( 'records' );

		if( is_array( $records ) ) {
			foreach( $records as $record ) {
				$recordID = AbstractRequest::secureData( $record, 'ID', 'int' );

				if( $recordID ) {
					$recordIDs[] = $recordID;
				}
			}
		}

		// load the web pages that should be deleted
		$webPageFeeder = new cDBFeederBase( '\\Inspirio\\cWebPage', $this->database );
		$webPages      = $webPageFeeder->whereColumnIn( 'ID', $recordIDs )->get();

		// extract some data used later
		$webPageIDs  = array();
		$blockSetIDs = array();
		foreach( $webPages as $webPage ) {
			$webPageID  = $webPage->getID();
			$blockSetID = $webPage->getBlockSetID();

			$webPageIDs[] = $webPageID;

			if( $blockSetID ) {
				$blockSetIDs[] = $blockSetID;
			}
		}

		try {
			$this->database->transactionStart();

			// delete associated data
			$response = $this->deleteAssociatedData( $request, $webPages );

			if( $response->getSuccess() ) {
				// delete images
				$imageHandler = new cImageHandler( $this->database );
				$images       = $imageHandler->getImageFeeder()->whereColumnIn( 'web_page_ID', $webPageIDs )->get();
				$imageHandler->deleteImages( $images );

				// delete webPages
				$webPageFeeder->whereColumnIn( 'ID', $webPageIDs )->remove();

				// delete templates
				$blockSetFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockSet', $this->database );
				$blockSetFeeder->whereColumnIn( 'ID', $blockSetIDs)->remove();

				$this->database->transactionCommit();

			} else {
				$this->database->transactionRollback();
			}

		} catch( Exception $e ) {
			$this->database->transactionRollback();
			throw $e;
		}

		return $response;
	}
}
