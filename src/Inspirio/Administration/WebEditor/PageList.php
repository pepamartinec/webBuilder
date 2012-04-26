<?php
namespace Inspirio\Administration\WebEditor;

use ExtAdmin\Response\ActionResponse;

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
	 * @var \SimpleXmlElement
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
	public function actions()
	{
		return array(
			'loadListData' => true,

			'createMenuItem' => array(
				'title'  => 'Položku menu',
				'type'   => 'create',
				'dataDep' => true,
				'params' => array(
					'editor'      => 'MenuItemEditor',
					'loadDefault' => 'loadData_new',
				),
			),

			'editMenuItem' => array(
				'title'  => 'Upravit',
				'type'   => 'edit',
				'params' => array(
					'editor'      => 'MenuItemEditor',
					'loadDefault' => 'loadData_record',
				),
			),

 			'deleteMenuItem' => array(
				'title'   => 'Smazat',
 				'type'    => 'delete',
 			),

			'createPage' => array(
				'title'  => 'Stránku',
				'type'   => 'create',
				'dataDep' => true,
				'params' => array(
					'editor'      => 'SimplePageEditor',
					'loadDefault' => 'loadData_new',
				),
			),

			'editPage' => array(
				'title'  => 'Upravit',
				'type'   => 'edit',
				'params' => array(
					'editor'      => 'SimplePageEditor',
					'loadDefault' => 'loadData_record',
				),
			),

 			'deletePage' => array(
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
			'barActions' => array(
				array(
					'type'  => 'splitButton',
					'title' => 'Založit nový',
					'items' => array( 'createMenuItem', 'createPage' )
				),
			),

			'fields' => array(
				'title' => array(
					'title' => 'Název',
					'type'  => 'treecolumn'
				),

				'urlName' => array(
					'title' => 'URL',
					'width' => 250,
				),

				'editedOn' => array(
					'title' => 'Poslední úprava',
					'type'  => 'datecolumn',
					'width' => 200,
				),

				'actions' => array(
					'type'  => 'actioncolumn',
					'items' => array( 'editPage', 'deletePage', 'editMenuItem', 'deleteMenuItem' )
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
			return new ActionResponse( true );
		}

		$extractor = function( cWebPage $webPage ) use( &$extractor, $webPages ) {
			$ID = $webPage->getID();

			if( isset( $webPages[ $ID ] ) ) {
				$children = $webPages[ $ID ];
			} else {
				$children = array();
			}

			// FIXME ugly solution
			$actions = array( 'createMenuItem', 'createPage' );
			switch( $webPage->getType() ) {
				case 'simplePage': $actions[] = 'editPage'; break;
				case 'menuItem'  : $actions[] = 'editMenuItem'; break;
			}

			if( $webPage->getParentID() ) {
				switch( $webPage->getType() ) {
					case 'simplePage': $actions[] = 'deletePage'; break;
					case 'menuItem'  : $actions[] = 'deleteMenuItem'; break;
				}
			}


			return array(
				'ID'       => $webPage->getID(),
				'title'    => $webPage->getTitle(),
				'urlName'  => $webPage->getUrlName(),
				'editedOn' => $webPage->getEditedOn() ?: $webPage->getCreatedOn(),
				'actions'  => $actions,
				'data'     => array_map( $extractor, $children ),
			);
		};

		$response = new ActionResponse( true );
		$response->setData( array_map( $extractor, $webPages[''] ) );

		return $response;
	}

	/**
	 * Deletes menuItems
	 *
	 * @param  RequestInterface $request
	 * @return Response
	 */
	public function deleteMenuItem( RequestInterface $request )
	{
		$editor = new MenuItemEditor( $this->database, $this->labels );

		return $editor->deleteData( $request );
	}

	/**
	 * Deletes webPage
	 *
	 * @param  RequestInterface $request
	 * @return Response
	 */
	public function deletePage( RequestInterface $request )
	{
		$editor = new SimplePageEditor( $this->database, $this->labels );

		return $editor->deleteData( $request );
	}
}