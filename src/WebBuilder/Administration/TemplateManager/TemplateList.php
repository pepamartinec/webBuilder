<?php
namespace WebBuilder\Administration\TemplateManager;

use ExtAdmin\Request\AbstractRequest;

use ExtAdmin\Response\ActionResponse;

use ExtAdmin\Module\DataBrowser\GridList;
use WebBuilder\DataObjects\BlockSet;
use ExtAdmin\Request\DataRequestDecorator;
use ExtAdmin\Response\DataBrowserResponse;
use ExtAdmin\RequestInterface;
use Inspirio\Database\cDBFeederBase;
use Inspirio\Database\cDatabase;

class TemplateList extends GridList
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

			'createEmpty' => array(
				'title'   => 'Vytvořit prázdnou',
				'type'    => 'create',
				'params'  => array(
					'editor'      => 'TemplateEditor',
					'loadDefault' => 'loadData_new'
				),
			),

			'createCopy' => array(
				'title'   => 'Vytvořit kopii',
				'type'    => 'create',
				'dataDep' => true,
				'params'  => array(
					'editor'      => 'TemplateEditor',
					'loadDefault' => 'loadData_copy'
				),
			),

			'createInherited' => array(
				'title'   => 'Vytvořit poděděnou',
				'type'    => 'create',
				'dataDep' => true,
				'params'  => array(
					'editor'      => 'TemplateEditor',
					'loadDefault' => 'loadData_inherited'
				),
			),

			'edit' => array(
				'title'  => 'Upravit',
				'type'   => 'edit',
				'params' => array(
					'editor'      => 'TemplateEditor',
					'loadDefault' => 'loadData_record'
				),
			),

			'delete' => array(
				'title'   => 'Smazat',
				'type'    => 'delete',
// 				'enabled' => function( BlockSet $record ) {
// 					return ( $record->getID() % 2 ) == 0;
// 				}
			),
		);
	}

	/**
	 * Module viewConfiguration
	 *
	 * @return array
	 */
	public function viewConfiguration()
	{
		return array(
			'barActions' => array(
				array(
					'type'  => 'splitButton',
					'title' => 'Založit novou',
					'items' => array( 'createEmpty', 'createCopy', 'createInherited' )
				),
				'edit',
				'delete'
			),

			'fields' => array(
				'title' => array(
					'title' => 'Název'
				),

				'algo' => array(
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

		$dataFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockSet', $this->database );
// 		$data       = $dataFeeder->where( 'ID NOT IN ( SELECT block_set_ID FROM web_pages )' )->get();
// 		$count      = $dataFeeder->where( 'ID NOT IN ( SELECT block_set_ID FROM web_pages )' )->getCount();

		$data       = $dataFeeder->get();
		$count      = $dataFeeder->getCount();

		if( $data === null ) {
			$data = array();
		}

		return new DataBrowserResponse( true, $data, $count, function( BlockSet $record ) {
			return array(
				'ID'    => $record->getID(),
				'title' => $record->getName(),
				'image' => 'images/templateThumb.png',
			);
		} );
	}

	/**
	 * Deletes the records
	 *
	 * @param RequestInterface $request
	 * @return ActionResponse
	 */
	public function delete( RequestInterface $request )
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

			$webPageFeeder = new cDBFeederBase( '\\WebBuilder\\DataObjects\\BlockSet', $this->database );
			$webPageFeeder->whereColumnIn( 'ID', $recordIDs )->remove();

			$this->database->transactionCommit();

		} catch( Exception $e ) {
			$this->database->transactionRollback();
			throw $e;
		}

		return new ActionResponse( true );
	}
}