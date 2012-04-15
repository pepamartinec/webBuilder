<?php
namespace Inspirio\Administration\WebEditor;

use ExtAdmin\Response\ResponseTest;

use ExtAdmin\Response\DataBrowserResponse;
use Inspirio\Database\cDBFeederBase;
use ExtAdmin\Request\DataRequestDecorator;
use ExtAdmin\RequestInterface;
use Inspirio\Database\cDatabase;
use ExtAdmin\Module\DataBrowser\GridList;
use Inspirio\cWebPage;

class PageList extends GridList
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
					'editor'     => 'PageEditor',
					'loadAction' => 'loadData_new',
				),
			),

			'edit' => array(
				'title'  => 'Upravit',
				'type'   => 'edit',
				'params' => array(
					'editor'      => 'PageEditor',
					'loadDefault' => 'loadData_record',
				),
			),

 			'delete' => array(
				'title'   => 'Smazat',
 				'type'    => 'delete',
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
					'title' => 'Název'
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

		$dataFeeder = new cDBFeederBase( '\\Inspirio\\cWebPage', $this->database );
		$data       = $dataFeeder->get();

		return new DataBrowserResponse( true, $data, sizeof( $data ), function( cWebPage $webPage ) {
			return $webPage->getInnerValues();
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

	}
}