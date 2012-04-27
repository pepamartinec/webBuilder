<?php
namespace DemoCMS\Administration\WebEditor;

use ExtAdmin\Request\AbstractRequest;

use DemoCMS\cWebPage;
use Inspirio\Database\cDBFeederBase;
use Inspirio\Database\cDatabase;
use ExtAdmin\Response\ActionResponse;
use ExtAdmin\RequestInterface;
use ExtAdmin\Module\DataEditor\DataEditor;

/**
 * MenuItemEditor class
 *
 */
class MenuItemEditor extends DataEditor
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
	 * Module view configuration
	 *
	 * @return array
	 */
	public function viewConfiguration()
	{
		return array(
			'type'       => 'DemoCMS.module.WebEditor.MenuItemEditor',
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
	 * Loads the editor data of an existing record
	 *
	 * @param RequestInterface $request
	 * @return ActionResponse
	 */
	public function loadData_record( RequestInterface $request )
	{
		$recordID = $request->getData( 'ID', 'int' );

		// load web page
		$webPageFeeder = new cDBFeederBase( '\\DemoCMS\\cWebPage', $this->database );
		$webPage       = $webPageFeeder->whereID( $recordID )->getOne();

		$data = array(
			'ID'        => $webPage->getID(),
			'parentID'  => $webPage->getParentID(),
			'title'     => $webPage->getTitle(),
			'published' => $webPage->getPublished(),
		);

		$response = new ActionResponse( true );
		$response->setData( $data );

		return $response;
	}

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

			// save webPage
			$webPage = new cWebPage( array(
				'ID'         => $request->getData( 'ID', 'int' ),
				'parentID'   => $request->getData( 'parentID', 'int' ),
				'type'       => 'menuItem',
				'title'      => $request->getData( 'title', 'string' ),
				'published'  => $request->getData( 'published', 'bool' ),
			), true );

			$webPageFeeder = new cDBFeederBase( '\\DemoCMS\\cWebPage', $this->database );
			$webPageFeeder->save( $webPage );

			$this->database->transactionCommit();

		} catch( Exception $e ) {
			$this->database->transactionRollback();
			throw $e;
		}

		$response = new ActionResponse( true );
		$response->setData( array(
			'ID'         => $webPage->getID(),
			'parentID'   => $webPage->getParentID(),
			'title'      => $webPage->getTitle(),
			'published'  => $webPage->getPublished(),
		) );

		return $response;
	}

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

		try {
			$this->database->transactionStart();

			$webPageFeeder = new cDBFeederBase( '\\DemoCMS\\cWebPage', $this->database );
			$webPages      = $webPageFeeder->whereColumnIn( 'ID', $recordIDs )->remove();

			$this->database->transactionCommit();

		} catch( Exception $e ) {
			$this->database->transactionRollback();
			throw $e;
		}

		return new ActionResponse( true );
	}
}
