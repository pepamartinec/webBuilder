<?php
namespace DemoCMS\Administration\TextBlockManager;

use ExtAdmin\Request\AbstractRequest;

use Inspirio\Database\cDatabase;
use DemoCMS\cTextBlock;
use Inspirio\Database\cDBFeederBase;
use ExtAdmin\Module\DataBrowser\GridList;
use ExtAdmin\RequestInterface;
use ExtAdmin\Request\DataRequestDecorator;
use ExtAdmin\Response\ActionResponse;
use ExtAdmin\Response\DataBrowserResponse;

class TextBlockList extends GridList
{
	/**
	 * @var cDatabase
	 */
	protected $database;

	/**
	 * Module constructor
	 *
	 * @param cDatabase $database
	 * @param \SimpleXMLElement $labels
	 */
	public function __construct( cDatabase $database, \SimpleXMLElement $labels )
	{
		$this->database = $database;
	}

	/**
	 * Returns module actions definition
	 *
	 * Used for defining actions within concrete modules implementations
	 *
	 * @return array
	 */
	public function actions()
	{
		return array(
				'loadListData' => true,

				'create' => array(
					'title'  => 'Vytvořit',
					'type'   => 'create',
					'params' => array(
						'editor'     => 'TextBlockEditor',
						'loadAction' => 'loadData_new',
					),
				),

				'edit' => array(
					'title'  => 'Upravit',
					'type'   => 'edit',
					'params' => array(
						'editor'      => 'TextBlockEditor',
						'loadDefault' => 'loadData_record',
					),
				),

				'delete' => array(
					'title' => 'Smazat',
					'type'  => 'delete',
				),
		);
	}

	/**
	 * Module UI viewConfiguration
	 *
	 * @return array
	 */
	public function viewConfiguration()
	{
		return array(
			'barActions' => array( 'create', 'edit', 'delete' ),

			'fields' => array(
				'title' => array(
					'title' => 'Název',
				),

				'actions' => array(
					'type'  => 'actioncolumn',
					'items' => array( 'edit', 'delete' )
				)
			),
		);
	}

	/**
	 * Loads data for dataList
	 *
	 * @param  RequestInterface $request
	 * @return DataBrowserResponse
	 */
	public function loadListData( RequestInterface $request )
	{
		$request = new DataRequestDecorator( $request );

		$textBlockFeeder = new cDBFeederBase( '\\DemoCMS\\cTextBlock', $this->database );

		foreach( $request->getOrdering() as $column => $dir ) {
			$textBlockFeeder->orderBy( $column, $dir );
		}

		$limit = $request->getLimit();
		$textBlockFeeder->limit( $limit[0], $limit[1] );

		$textBlocks = $textBlockFeeder->get();
		$total      = $textBlockFeeder->getCount();

		if( $textBlocks === null ) {
			$textBlocks = array();
		}

		return new DataBrowserResponse( true, $textBlocks, $total, function( cTextBlock $textBlock ) {
			return array(
				'ID'      => $textBlock->getID(),
				'title'   => $textBlock->getTitle(),
				'imageID' => $textBlock->getImageID(),
				'content' => $textBlock->getContent(),
			);
		});
	}

	/**
	 * Deletes selected items
	 *
	 * @param  RequestInterface $request
	 * @return Response
	 */
	public function delete( RequestInterface $request )
	{
		$records = $request->getRawData( 'records' );
		if( is_array( $records ) ) {
			$recordIDs = array();

			foreach( $records as $record ) {
				$recordIDs[] = AbstractRequest::secureData( $record, 'ID', 'int' );
			}

			$textBlockFeeder = new cDBFeederBase( '\\DemoCMS\\cTextBlock', $this->database );
			$textBlockFeeder->whereColumnIn( 'ID', $recordIDs )->remove();

			return new ActionResponse( true );

		} else {
			$response = new ActionResponse( false );
			$response->setMessage( 'Neplatný požadavek' );
			return $response;
		}
	}
}
