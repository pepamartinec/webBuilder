<?php
namespace DemoCMS\Administration\WebEditor;

use ExtAdmin\Response\ActionResponse;

use ExtAdmin\Response\DataBrowserResponse;
use Inspirio\Database\cDBFeederBase;
use ExtAdmin\Request\DataRequestDecorator;
use ExtAdmin\RequestInterface;
use Inspirio\Database\cDatabase;
use ExtAdmin\Module\DataBrowser\TreeList;
use DemoCMS\cWebPage;

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
			'moveItem'     => true,

			'createMenuItem' => array(
				'title'   => 'Založit prázdnou položku menu',
				'type'    => 'create',
				'iconCls' => 'i-menu-item',
				'dataDep' => true,
				'params'  => array(
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
				'title'   => 'Založit novou stránku',
				'type'    => 'create',
				'iconCls' => 'i-simple-page',
				'dataDep' => true,
				'params'  => array(
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
			'barActions' => array( 'createPage', 'createMenuItem' ),

			'moveAction' => 'moveItem',

			'fields' => array(
				'title' => array(
					'title' => 'Název',
					'type'  => 'treecolumn'
				),

				'published' => array(
					'title' => 'Publikováno',
					'width' => 80,
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

				'iconCls' => array(
					'display' => false
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

		$webPageFeeder = new cDBFeederBase( '\\DemoCMS\\cWebPage', $this->database );
		$webPages      = $webPageFeeder->groupBy( 'parent_ID' )
		                               ->orderBy( 'parent_ID', 'asc' )
		                               ->orderBy( 'position', 'asc' )
		                               ->get();

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

			if( $webPage->getPublished() ) {
				$published = "<img src='public/images/icons/eye.png' title='Publikováno' alt='Publikováno' />";
			} else {
				$published = "<img src='public/images/icons/eye_gray.png' title='Nepublikováno' alt='Nepublikováno' />";
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
				'ID'        => $webPage->getID(),
				'title'     => $webPage->getTitle(),
				'urlName'   => $webPage->getUrlName(),
				'published' => $published,
				'editedOn'  => $webPage->getEditedOn() ?: $webPage->getCreatedOn(),
				'iconCls'   => $webPage->getType(),
				'actions'   => $actions,
				'data'      => array_map( $extractor, $children ),
			);
		};

		$response = new ActionResponse( true );
		$response->setData( array_map( $extractor, $webPages[''] ) );

		return $response;
	}

	/**
	 * Changes position of the item within the tree
	 *
	 * @param RequestInterface $request
	 * @return ActionResponse
	 */
	public function moveItem( RequestInterface $request )
	{
		$itemID   = $request->getData( 'itemID', 'int' );
		$parentID = $request->getData( 'parentID', 'int' );
		$position = $request->getData( 'position', 'int' );

		if( $itemID == null || $parentID == null || $itemID == $parentID ) {
			return new ActionResponse( false );
		}

		$webPageFeeder = new cDBFeederBase( '\\DemoCMS\\cWebPage', $this->database );
		$webPages      = $webPageFeeder->whereColumnIn( 'ID', array( $itemID, $parentID ) )->indexBy( 'ID' )->get();

		if( ! isset( $webPages[ $itemID ], $webPages[ $parentID ] ) ) {
			return new ActionResponse( false );
		}

		$item = $webPages[ $itemID ];

		if( $position < $item->getPosition() ) {
			++$position;
		}


		$item->setParentID( $parentID );
		$item->setPosition( $position );

		$webPageFeeder->save( $item );

		return new ActionResponse( true );
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