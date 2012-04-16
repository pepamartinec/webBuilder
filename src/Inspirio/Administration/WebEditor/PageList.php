<?php
namespace Inspirio\Administration\WebEditor;

use ExtAdmin\Response\DataBrowserResponse;
use Inspirio\Database\cDBFeederBase;
use ExtAdmin\Request\DataRequestDecorator;
use ExtAdmin\RequestInterface;
use Inspirio\Database\cDatabase;
use ExtAdmin\Module\DataBrowser\TreeList;
use Inspirio\cWebPage;

class PageList extends TreeList
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
				'dataDep' => true,
				'params' => array(
					'editor'     => 'SimplePageEditor',
					'loadAction' => 'loadData_new',
				),
			),

			'edit' => array(
				'title'  => 'Upravit',
				'type'   => 'edit',
				'params' => array(
					'editor'      => 'SimplePageEditor',
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
					'title' => 'Název',
					'type'  => 'treecolumn'
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

		$webPageFeeder = new cDBFeederBase( '\\Inspirio\\cWebPage', $this->database );
		$webPages      = $webPageFeeder->groupBy( 'parent_ID' )->orderBy( 'parent_ID', 'asc' )->get();

		if( $webPages === null ) {
			$webPages = array();
		}

		$extractor = function( cWebPage $webPage ) use( &$extractor, $webPages ) {
			$ID = $webPage->getID();

			if( isset( $webPages[ $ID ] ) ) {
				$children = $webPages[ $ID ];
			} else {
				$children = array();
			}

			return array(
				'ID'      => $webPage->getID(),
				'title'   => $webPage->getTitle(),
				'actions' => array( 'create', 'edit', 'delete' ),
				'data'    => array_map( $extractor, $children ),
			);
		};

		return new DataBrowserResponse( true, $webPages[''], 0, $extractor );
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